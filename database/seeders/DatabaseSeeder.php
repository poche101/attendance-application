<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Seeding Church Members...');

        // Admin User
        User::updateOrCreate(['email' => 'admin@church.org'], [
            'name'     => 'Church Admin',
            'email'    => 'admin@church.org',
            'password' => Hash::make('password'),
        ]);

        $csvPath = database_path('seeders/data/members.csv');

        if (!file_exists($csvPath)) {
            $this->command->error("❌ CSV not found at: {$csvPath}");
            return;
        }

        $this->command->info("📂 Processing CSV...");

        $lines = file($csvPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        array_shift($lines); // Remove header

        $count = 0;

        foreach ($lines as $line) {
            // Remove outer quotes and parse
            $line = trim($line, '"');
            $row = str_getcsv($line, ",", "\"", "\\");

            if (count($row) < 7) {
                continue;
            }

            $email = strtolower(trim($row[6] ?? ''));
            if (empty($email) || !str_contains($email, '@')) {
                continue;
            }

            $phone = $this->cleanPhone($row[5] ?? '');

            $memberData = [
                'title'      => trim($row[0] ?? ''),
                'first_name' => trim($row[1] ?? ''),
                'last_name'  => trim($row[2] ?? ''),
                'church'     => trim($row[3] ?? 'Lekki'),
                'cell'       => trim($row[4] ?? ''),
                'phone'      => $phone,
                'email'      => $email,
                'is_active'  => true,
            ];

            Member::updateOrCreate(['email' => $email], $memberData);

            $count++;

            if ($count % 1000 === 0) {
                $this->command->info("✅ Seeded {$count} members...");
            }
        }

        $this->command->info("🎉 Successfully seeded {$count} members!");

        $this->seedSampleAttendance();
    }

    private function cleanPhone(string $phone): string
    {
        $phone = trim($phone);
        if (empty($phone)) return '';

        if (stripos($phone, 'E') !== false) {
            $phone = (string) (int) floatval($phone);
        }

        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (strlen($phone) === 11 && str_starts_with($phone, '0')) {
            $phone = '234' . substr($phone, 1);
        }

        return $phone;
    }

    private function seedSampleAttendance(): void
    {
        $today = Carbon::today()->toDateString();
        $samples = ['seyitade@yahoo.com', 'oluwadarelord@yahoo.com', 'esosaking@gmail.com'];

        foreach ($samples as $email) {
            $member = Member::where('email', $email)->first();
            if ($member) {
                Attendance::updateOrCreate(
                    ['member_id' => $member->id, 'attendance_date' => $today],
                    ['submitted_at' => now()]
                );
            }
        }
    }
}
