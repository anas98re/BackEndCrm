<?php

namespace App\Services\TaskManangement;

use App\Services\JsonResponeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class queriesService extends JsonResponeService
{
    public function getAllinfoQuery()
    {
        return DB::table('tasks')
            ->select('tasks.*', 'task_statuses.*', 'statuse_task_fraction.*', 'assigned_by_user.*', 'assigned_to_user.*')
            ->leftJoin('statuse_task_fraction', 'tasks.id', '=', 'statuse_task_fraction.task_id')
            ->leftJoin('task_statuses', 'statuse_task_fraction.task_statuse_id', '=', 'task_statuses.id')
            ->leftJoin('users as assigned_by_user', 'tasks.assigned_by', '=', 'assigned_by_user.id_user')
            ->leftJoin('users as assigned_to_user', 'tasks.assigned_to', '=', 'assigned_to_user.id_user');
    }

    public function calucateTheHoursFromDeadline($start_date, $deadline)
    {
        $startDateTime = Carbon::parse($start_date);
        $endDateTime = Carbon::parse($deadline);
        $diffHours = $startDateTime->diffInHoursFiltered(function ($date) {
            return $date->dayOfWeek !== Carbon::FRIDAY;
        }, $endDateTime);
        $numberOfActualHours = $diffHours / 2.8235294118;

        $totalMinutes = round($numberOfActualHours * 60);
        $hours = floor($totalMinutes / 60);
        $minutes = $totalMinutes % 60;
        $resultOfHouresWithoutFriday = $hours . ':' . $minutes;
        return $resultOfHouresWithoutFriday;
    }
}
