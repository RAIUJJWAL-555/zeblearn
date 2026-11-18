<?php

namespace App\Console\Commands;

use App\Services\BlogRecoveryService;
use Illuminate\Console\Command;

class RecoverLegacyBlogsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blog:recover {--limit= : Maximum number of posts to process} {--force : Overwrite existing blogs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recover and import legacy WordPress blogs from sap.zeblearn.com';

    /**
     * Execute the console command.
     */
    public function handle(BlogRecoveryService $service): int
    {
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $force = (bool) $this->option('force');

        $this->info('Starting ZebLearn Legacy Blog Recovery...');
        if ($limit) {
            $this->info("Processing limit: {$limit} blogs");
        }

        $result = $service->runMigration($limit, $force);

        $this->newLine();
        $this->table(
            ['Metric', 'Count'],
            [
                ['Batch ID', $result['batch_id'] ?? 'N/A'],
                ['Total Processed', $result['total_processed']],
                ['Imported (Full Content -> Draft)', $result['imported']],
                ['Incomplete (Excerpt only)', $result['incomplete']],
                ['Duplicates (Skipped)', $result['duplicates']],
                ['Failed', $result['failed']],
            ]
        );

        $this->info($result['message']);

        return self::SUCCESS;
    }
}
