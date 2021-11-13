<?php

namespace Database\Seeders;

use App\Models\Supplier;

class SupplierSeeder extends BaseSeeder
{

    protected $cleanTables = [
        'suppliers'
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Supplier::factory()->count(400)->create();
    }
}
