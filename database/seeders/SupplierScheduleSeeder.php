<?php

namespace Database\Seeders;


use App\Models\Supplier;
use App\Models\SupplierSchedule;

class SupplierScheduleSeeder extends BaseSeeder
{
    protected $cleanTables = [
        'supplier_schedules'
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->progressStart(Supplier::query()->count(), 'Start Seeding supplier\'s schedule');
        Supplier::query()->chunk(10, function ($suppliers) {
            foreach ($suppliers as $supplier) {
                SupplierSchedule::factory()->create([
                    'supplier_id' => $supplier
                ]);
                $this->progressAdvance();
            }
        });
        $this->progressFinish();
    }
}
