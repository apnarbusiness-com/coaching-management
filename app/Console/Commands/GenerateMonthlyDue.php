<?php

namespace App\Console\Commands;

use App\Services\DueCalculationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMonthlyDue extends Command
{
    protected $signature = 'due:generate-monthly {--month= : Specific month (1-12)} {--year= : Specific year} {--dry-run : Preview without saving}';

    protected $description = 'Generate monthly due records for all enrolled students';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(DueCalculationService $service): int
    {
        $month = $this->option('month') ? (int) $this->option('month') : Carbon::now()->month;
        $year = $this->option('year') ? (int) $this->option('year') : Carbon::now()->year;
        $dryRun = $this->option('dry-run');

        $this->info("Generating dues for month: {$month}/{$year}");

        if ($dryRun) {
            $this->warn('Dry run mode - no records will be saved');
        }

        if ($dryRun) {
            return Command::SUCCESS;
        }

        $results = $service->generateMonthlyDues($month, $year);

        $this->info("Monthly dues generated successfully!");
        $this->table(
            ['Type', 'Count'],
            [
                ['Monthly Students', $results['monthly_generated']],
                ['Course-wise Students', $results['course_generated']],
                ['Skipped (existing/already enrolled)', $results['skipped']],
            ]
        );

        if (!empty($results['errors'])) {
            $this->error('Errors encountered:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        return Command::SUCCESS;
    }
}
