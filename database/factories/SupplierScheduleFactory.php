<?php

namespace Database\Factories;

use App\Models\SupplierSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierScheduleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SupplierSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'started_at' => now()
                ->subDays(7)
                ->startOfWeek()
                ->addHours(4)
                ->addMinutes(30),

            'ended_at' => now()
                ->subDays(7)
                ->startOfWeek()
                ->addDays(4)
                ->subHours(12)
                ->addMinutes(30),
        ];
    }
}
