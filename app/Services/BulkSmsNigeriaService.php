<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkSmsNigeriaService
{
    protected string $baseUrl;
    protected ?string $token;
    protected string $senderId;

    public function __construct()
    {
        $this->baseUrl  = config('bulksmsnigeria.base_url', 'https://www.bulksmsnigeria.com/api/v2');
        $this->token    = config('bulksmsnigeria.token');
        $this->senderId = config('bulksmsnigeria.sender_id', 'Church');
    }

    /**
     * Convert a locally-stored Nigerian number (e.g. "08012345678" or "+2348012345678")
     * into the international format BulkSMSNigeria requires (e.g. "2348012345678").
     * Returns null if the number doesn't look like a valid Nigerian mobile number.
     */
    public function toInternationalFormat(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // Already in international format: 234 + 10 digits = 13 digits total
        if (str_starts_with($digits, '234') && strlen($digits) === 13) {
            return $digits;
        }

        // Local format: 0 + 10 digits = 11 digits total
        if (str_starts_with($digits, '0') && strlen($digits) === 11) {
            return '234' . substr($digits, 1);
        }

        return null;
    }

    /**
     * Send a single message to many recipients in one API call.
     *
     * @param  array<int, string>  $phones
     * @param  string  $message
     * @return array{
     *     success: bool,
     *     sent_to: array<int, string>,
     *     skipped: array<int, string>,
     *     error: string|null,
     *     message_id: string|null
     * }
     */
    public function sendToMany(array $phones, string $message): array
    {
        $recipients = [];
        $skipped    = [];

        foreach ($phones as $phone) {
            $formatted = $this->toInternationalFormat((string) $phone);
            if ($formatted) {
                $recipients[] = $formatted;
            } else {
                $skipped[] = $phone;
            }
        }

        // De-duplicate recipients
        $recipients = array_values(array_unique($recipients));

        if (empty($recipients)) {
            return [
                'success'    => false,
                'sent_to'    => [],
                'skipped'    => $skipped,
                'error'      => 'No valid Nigerian phone numbers to send to.',
                'message_id' => null,
            ];
        }

        if (empty($this->token)) {
            return [
                'success'    => false,
                'sent_to'    => [],
                'skipped'    => $skipped,
                'error'      => 'BulkSMSNigeria API token is not configured (BULKSMSNIGERIA_API_TOKEN in .env).',
                'message_id' => null,
            ];
        }

        try {
            // FIX: Pass api_token in body instead of withToken() header
            $response = Http::acceptJson()
                ->timeout(15)
                ->post("{$this->baseUrl}/sms/create", [
                    'api_token' => $this->token,
                    'from'      => $this->senderId,
                    'to'        => implode(',', $recipients),
                    'body'      => $message,
                ]);

            $payload = $response->json();

            if ($response->successful() && ($payload['status'] ?? null) === 'success') {
                return [
                    'success'    => true,
                    'sent_to'    => $recipients,
                    'skipped'    => $skipped,
                    'error'      => null,
                    'message_id' => $payload['data']['message_id'] ?? $payload['data']['id'] ?? null,
                ];
            }

            Log::error('BulkSMSNigeria send failed', [
                'status_code' => $response->status(),
                'body'        => $response->body(),
            ]);

            return [
                'success'    => false,
                'sent_to'    => [],
                'skipped'    => $skipped,
                'error'      => $payload['message'] ?? $payload['error']['message'] ?? 'SMS provider returned an error.',
                'message_id' => null,
            ];
        } catch (\Throwable $e) {
            Log::error('BulkSMSNigeria request exception: ' . $e->getMessage());

            return [
                'success'    => false,
                'sent_to'    => [],
                'skipped'    => $skipped,
                'error'      => 'Could not reach the SMS provider. Please try again shortly.',
                'message_id' => null,
            ];
        }
    }
}
