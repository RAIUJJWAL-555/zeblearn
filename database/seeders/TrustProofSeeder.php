<?php

namespace Database\Seeders;

use App\Services\TrustProofService;
use Illuminate\Database\Seeder;

class TrustProofSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(TrustProofService $service): void
    {
        $service->seedInitialTrustRecords();
    }
}
