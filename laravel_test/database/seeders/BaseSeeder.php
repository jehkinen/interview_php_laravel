<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BaseSeeder extends Seeder
{
    protected $cleanTables = [];

    public function __construct()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->beforeCleanTables();
        $this->clean();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

    }

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

    public function info($message)
    {
        $this->command->info($message);
    }

    public function progressFinish()
    {
        $this->command->getOutput()->progressFinish();
    }

    public function progressAdvance()
    {
        $this->command->getOutput()->progressAdvance();
    }

    public function progressStart($counter, $message = null)
    {
        $message = $message ?? 'Start seeding ' . static::class;

        $this->info($message);

        $this->command->getOutput()->progressStart($counter);
    }

}
