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

    public function getClientsThatIsNoUpdateToTheLatestClientUpdatesFor5Days()
    {
        $query = DB::table('clients as u')
            ->select(
                'clcomm.date_comment as dateCommentClient',
                'u.*',
                'c.nameCountry',
                'r.name_regoin',
                'us.nameUser',
                'r.fk_country'
            )
            ->leftJoin('regoin as r', 'r.id_regoin', '=', 'u.fk_regoin')
            ->leftJoin('country as c', 'c.id_country', '=', 'r.fk_country')
            ->join('users as us', 'us.id_user', '=', 'u.fk_user')
            ->leftJoin('users as uuserss', 'uuserss.id_user', '=', 'u.user_add')
            ->leftJoin('client_comment as clcomm', function ($join) {
                $join->on('clcomm.fk_client', '=', 'u.id_clients')
                    ->where('clcomm.date_comment', '=', function ($subquery) {
                        $subquery->select(DB::raw('MAX(date_comment)'))
                            ->from('client_comment')
                            ->whereRaw('client_comment.fk_client = u.id_clients');
                    });
            })
            ->where('u.is_comments_check', '=', 0)
            ->where('u.type_client', '=', 'تفاوض')
            ->where('u.date_create', '>=', '2023-08-01')
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('u.ismarketing', '=', 1)
                        ->whereRaw('DATEDIFF(clcomm.date_comment, u.date_create) > 5');
                })
                    ->orWhere(function ($q) {
                        $q->where('u.ismarketing', '!=', 1)
                            ->whereRaw('DATEDIFF(clcomm.date_comment, u.date_create) > 3');
                    });
            })
            ->orderBy('dateCommentClient', 'ASC');

        return $query;
    }

    public function BranchSupervisorsToTheRequiredLevel($elementOfRegions, $typeLevel)
    {
        $users = collect();
        foreach ($elementOfRegions as $el) {
            $regoin = DB::table('regoin')->where('id_regoin', $el)->first();
            $usersQuery = DB::table('users as u')
                ->where(function ($query) use ($el, $typeLevel) {
                    $query->where('u.fk_regoin', $el)
                        ->whereIn('u.type_level', $typeLevel);
                })
                ->orWhere(function ($query) use ($regoin, $typeLevel) {
                    $query->where('u.fk_regoin', 14)
                        ->where('u.fk_country', $regoin->fk_country)
                        ->whereIn('u.type_level', $typeLevel);
                })
                ->get();
            $users = $users->concat($usersQuery);
        }

        return $users;
    }
}
