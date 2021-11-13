<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait CleanableSeedTrait
{
    public function clean()
    {
        $this->beforeCleanTables();

        foreach ($this->cleanTables as $table) {
            DB::table($table)->truncate();
            echo 'Table ' . $table . ' has been cleaned before seeding ' . "\n\r";
        }
        Schema::enableForeignKeyConstraints();
    }

    public function beforeCleanTables()
    {
    }
}
