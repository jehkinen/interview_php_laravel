<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupplierScheduleTable extends Migration
{
    const SUPPLIER_SCHEDULES = 'supplier_schedules';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(self::SUPPLIER_SCHEDULES, function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->timestamps();
        });

        Schema::table(self::SUPPLIER_SCHEDULES, function (Blueprint $table) {
            $table->foreign('supplier_id')->references('id')->on('suppliers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(self::SUPPLIER_SCHEDULES, function (Blueprint $table) {
            $table->dropForeign(['supplier_id']);
        });

        Schema::dropIfExists(self::SUPPLIER_SCHEDULES);
    }
}
