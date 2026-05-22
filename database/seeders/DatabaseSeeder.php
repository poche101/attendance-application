<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(['email' => 'admin@church.org'], [
            'name'     => 'Church Admin',
            'email'    => 'admin@church.org',
            'password' => Hash::make('admin123'),
        ]);

        // Path to your CSV file
        $csvPath = base_path('database/seeders/data/users.csv'); // Move your file here

        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found at: " . $csvPath);
            return;
        }

        $this->command->info('Starting to seed members from CSV...');

        $handle = fopen($csvPath, 'r');
        $header = fgetcsv($handle); // Skip header

        $count = 0;
        $batch = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty($row[7])) continue; // Skip if no email

            $memberData = [
                'title'      => $row[1] ?? null,
                'first_name' => $row[2] ?? null,
                'last_name'  => $row[3] ?? null,
                'email'      => $row[7] ?? null,
                'phone'      => $row[6] ?? null,
                'group'      => $row[5] ?? null,      // cell
                'church'     => $row[4] ?? null,      // church_id
                'cell'       => $row[5] ?? null,
                'birthday'   => !empty($row[10]) && $row[10] !== 'NULL' ? $row[10] : null,
                'is_active'  => true,
            ];

            Member::updateOrCreate(
                ['email' => $memberData['email']],
                $memberData
            );

            $count++;

            if ($count % 500 === 0) {
                $this->command->info("Seeded {$count} members...");
            }
        }

        fclose($handle);

        $this->command->info("✅ Successfully seeded {$count} members!");

        // Sample attendance
        $today = Carbon::today()->toDateString();
        $sampleEmails = [
            'seyitade@yahoo.com',
            'tyadeoye@gmail.com',
            'oluwadarelord@yahoo.com',
            'esosaking@gmail.com',
            'jeromemordi@yahoo.com',
        ];

        foreach ($sampleEmails as $email) {
            $member = Member::where('email', $email)->first();
            if ($member) {
                Attendance::updateOrCreate(
                    ['email' => $email, 'attendance_date' => $today],
                    ['member_id' => $member->id, 'submitted_at' => Carbon::now()]
                );
            }
        }
    }
}
