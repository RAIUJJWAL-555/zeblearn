<?php

namespace App\Console\Commands;

use App\Services\CertificateService;
use Illuminate\Console\Command;

class IssueEligibleCertificatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:issue-eligible';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan and safely issue certificates for all completed and eligible course enrollments.';

    /**
     * Execute the console command.
     */
    public function handle(CertificateService $certificateService): int
    {
        $this->info('Scanning approved enrollments for completed courses without certificates...');

        $count = $certificateService->issueAllEligibleCertificates();

        $this->info("Successfully issued {$count} certificate(s).");

        return self::SUCCESS;
    }
}
