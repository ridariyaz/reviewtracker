<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Feedback;
use App\Models\ScanLog;
use App\Models\User;
use App\Services\QrCodeService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'is_admin' => true,
            'provider' => 'local',
        ]);

        $company = Company::create([
            'user_id' => $user->id,
            'name' => 'Acme Deluxe Lounge',
            'primary_color' => '#0d6efd',
            'secondary_color' => '#020617',
            'google_review_url' => 'https://g.page/r/example/review',
            'tripadvisor_review_url' => 'https://www.tripadvisor.com/UserReview-example',
            'trustpilot_review_url' => 'https://www.trustpilot.com/evaluate/example',
        ]);

        $qrService = new QrCodeService();

        // Create Employees
        $alice = Employee::create([
            'company_id' => $company->id,
            'name' => 'Alice Johnson',
            'scans' => 28,
            'good_count' => 18,
            'ok_count' => 4,
            'bad_count' => 1,
            'employee_username' => 'alice',
            'employee_password' => 'password123',
        ]);
        $qrService->generateForEmployee($alice->id, route('review.show', $alice->id));

        $bob = Employee::create([
            'company_id' => $company->id,
            'name' => 'Bob Smith',
            'scans' => 19,
            'good_count' => 11,
            'ok_count' => 2,
            'bad_count' => 0,
            'employee_username' => 'bob',
            'employee_password' => 'password123',
        ]);
        $qrService->generateForEmployee($bob->id, route('review.show', $bob->id));

        $charlie = Employee::create([
            'company_id' => $company->id,
            'name' => 'Charlie Davis',
            'scans' => 12,
            'good_count' => 6,
            'ok_count' => 3,
            'bad_count' => 1,
            'employee_username' => 'charlie',
            'employee_password' => 'password123',
        ]);
        $qrService->generateForEmployee($charlie->id, route('review.show', $charlie->id));

        // Create Scan Logs across past days
        foreach (range(1, 14) as $daysAgo) {
            $date = now()->subDays($daysAgo);
            $scansCount = rand(2, 6);
            for ($i = 0; $i < $scansCount; $i++) {
                ScanLog::create([
                    'company_id' => $company->id,
                    'employee_id' => [$alice->id, $bob->id, $charlie->id][array_rand([$alice->id, $bob->id, $charlie->id])],
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
                    'device_type' => 'mobile',
                    'created_at' => $date,
                ]);
            }
        }

        // Create Feedback items
        Feedback::create([
            'company_id' => $company->id,
            'employee_id' => $alice->id,
            'rating' => 'good',
            'comment' => '',
            'status' => 'new',
            'created_at' => now()->subHours(2),
        ]);

        Feedback::create([
            'company_id' => $company->id,
            'employee_id' => $alice->id,
            'rating' => 'ok',
            'comment' => 'Service was a bit slow during rush hour, but staff was polite.',
            'status' => 'in_progress',
            'created_at' => now()->subDay(),
        ]);

        Feedback::create([
            'company_id' => $company->id,
            'employee_id' => $charlie->id,
            'rating' => 'bad',
            'comment' => 'Table was not clean when seated.',
            'status' => 'new',
            'created_at' => now()->subDays(2),
        ]);
    }
}
