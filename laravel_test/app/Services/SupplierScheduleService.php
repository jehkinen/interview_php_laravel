<?php


namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SupplierScheduleService
{

    /**
     * @param Carbon $startedAt
     * @param Carbon $endedAt
     * @return mixed
     */
    public function calculateTotalWorkedHours(Carbon $startedAt, Carbon $endedAt)
    {
        $totalWorkedHours = DB::selectOne('
            SELECT SUM(total_worked_hours) as value 
                FROM
                (SELECT 
                    SUM(TIMESTAMPDIFF(HOUR, started_at, ended_at)
                ) AS total_worked_hours 
                FROM
                    supplier_schedules 
                WHERE DATE(started_at) >= ? AND DATE(started_at) <= ?
                GROUP BY supplier_id) AS zzz 
        ', [$startedAt, $endedAt]);

        return (int) $totalWorkedHours->value;

    }
}
