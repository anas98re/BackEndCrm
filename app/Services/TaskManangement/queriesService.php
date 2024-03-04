<?php

namespace App\Services\TaskManangement;

use App\Constants;
use App\Models\users;
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
            // ->where('u.is_comments_check', '=', 0)
            // ->where('u.type_client', '=', 'تفاوض')
            ->where('u.date_create', '>=', Carbon::now('Asia/Riyadh')->subMonthsNoOverflow(1)->startOfMonth()->toDateString()) // get date which is the first day of the previous month.
            // ->where('u.date_create', '>=', Carbon::now('Asia/Riyadh')->startOfMonth()->toDateString()) // get date which is the first day of the previous month.
            ->where(function ($query) {
                $query->where('u.type_client', '=', 'تفاوض')
                    ->orWhere('u.type_client', '=', 'عرض سعر');
            })
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('u.ismarketing', '=', 1)
                        ->whereRaw('DATEDIFF(clcomm.date_comment, NOW()) > 3');
                })
                    ->orWhere(function ($q) {
                        $q->where('u.ismarketing', '!=', 1)
                            ->whereRaw('DATEDIFF(clcomm.date_comment, NOW()) > 5');
                    })
                    ->orWhere(function ($q) {
                        $q->where('u.ismarketing', '=', 1)
                            ->WhereRaw('DATEDIFF(NOW(), u.date_create) > 3');
                    })
                    ->orWhere(function ($q) {
                        $q->where('u.ismarketing', '!=', 1)
                            ->WhereRaw('DATEDIFF(NOW(), u.date_create) > 5');
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
                    $query->where('u.fk_regoin', Constants::ALL_BRUNSHES)
                        ->where('u.fk_country', $regoin->fk_country)
                        ->whereIn('u.type_level', $typeLevel);
                })
                ->get();
            $users = $users->concat($usersQuery);
        }

        return $users;
    }

    public function ToBothDepartmentAndRegionSupervisorsToTheRequiredLevelForTaskProcedures($value)
    {
        $theUser = users::where('id_user', $value)->first();
        $userRegion = $theUser->fk_regoin;
        $userDepartment = $theUser->type_administration;

        $typeLevel = [];
        $typeLevelForDepartment = [];
        $department = [];

        $privgLevelUsers = DB::table('privg_level_user')
            ->where('fk_privileg', 178)
            ->where('is_check', 1)
            ->get();
        foreach ($privgLevelUsers as $level) {
            $typeLevel[] = $level->fk_level;
        }

        $privgLevelUsersForDepartment = DB::table('privg_level_user')
            ->where('fk_privileg', 176)
            ->where('is_check', 1)
            ->get();
        foreach ($privgLevelUsersForDepartment as $level) {
            $typeLevelForDepartment[] = $level->fk_level;
        }

        $departmentsUsers = DB::table('users')
            ->whereIn('type_level', $typeLevelForDepartment)->get();
        foreach ($departmentsUsers as $departmentsUsers) {
            $department[] = $departmentsUsers->type_administration;
        }
        $departments = array_unique($department);


        $users = collect();
        $usersQuery = DB::table('users as u')
            ->where(function ($query) use ($userDepartment, $typeLevel) {
                $query->where('u.type_administration', $userDepartment)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->orWhere(function ($query) use ($typeLevel, $departments) {
                $query->whereIn('u.type_administration', $departments)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->orWhere(function ($query) use ($userRegion, $typeLevel) {
                $query->where('u.fk_regoin', $userRegion)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->orWhere(function ($query) use ($typeLevel) {
                $query->where('u.fk_regoin', Constants::ALL_BRUNSHES)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->get();
        $users = $users->concat($usersQuery);

        $IDs = [];
        foreach ($users as $el) {
            $IDs[] = $el->id_user;
        }
        return $IDs;
    }

    public function BranchSupervisorsToTheRequiredLevelForTaskProcedures($elementOfRegions)
    {
        $privgLevelUsers = DB::table('privg_level_user')
            ->where('fk_privileg', 178)
            ->where('is_check', 1)
            ->get();
        $typeLevel = [];
        foreach ($privgLevelUsers as $level) {
            $typeLevel[] = $level->fk_level;
        }
        $users = collect();
        $usersQuery = DB::table('users as u')
            ->where(function ($query) use ($elementOfRegions, $typeLevel) {
                $query->where('u.fk_regoin', $elementOfRegions)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->orWhere(function ($query) use ($typeLevel) {
                $query->where('u.fk_regoin', Constants::ALL_BRUNSHES)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->get();
        $users = $users->concat($usersQuery);

        $xIDs = [];
        foreach ($users as $el) {
            $xIDs[] = $el->id_user;
        }
        return $xIDs;
    }

    public function departmentSupervisorsToTheRequiredLevelForTaskProcedures($elementOfDepartments)
    {
        $typeLevel = [];
        $typeLevel2 = [];
        $department = [];

        $privgLevelUsers = DB::table('privg_level_user')
            ->where('fk_privileg', 178)
            ->where('is_check', 1)
            ->get();
        foreach ($privgLevelUsers as $level) {
            $typeLevel[] = $level->fk_level;
        }

        $privgLevelUsersForDepartment = DB::table('privg_level_user')
            ->where('fk_privileg', 176)
            ->where('is_check', 1)
            ->get();
        foreach ($privgLevelUsersForDepartment as $level) {
            $typeLevel2[] = $level->fk_level;
        }

        $departmentsUsers = DB::table('users')
            ->whereIn('type_level', $typeLevel2)->get();
        foreach ($departmentsUsers as $departmentsUsers) {
            $department[] = $departmentsUsers->type_administration;
        }
        $departments = array_unique($department);

        $users = collect();
        $usersQuery = DB::table('users as u')
            ->where(function ($query) use ($elementOfDepartments, $typeLevel) {
                $query->where('u.type_administration', $elementOfDepartments)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->orWhere(function ($query) use ($typeLevel, $departments) {
                $query->whereIn('u.type_administration', $departments)
                    ->whereIn('u.type_level', $typeLevel);
            })
            ->get();
        $users = $users->concat($usersQuery);

        $xIDs = [];
        foreach ($users as $el) {
            $xIDs[] = $el->id_user;
        }
        return $xIDs;
    }

    public function getRegionNamesAndDuplicates($duplicates)
    {
        $regionNames = DB::table('regoin')
            ->whereIn('id_regoin', array_keys($duplicates))
            ->pluck('name_regoin', 'id_regoin')
            ->toArray();

        $duplicatesWithName = [];

        foreach ($duplicates as $regionId => $count) {
            $regionName = $regionNames[$regionId];
            $duplicatesWithName[$regionName] = $count;
        }
        $duplicatesWithName;

        $message = ' هناك ? عميل في ! لم يُعلّق لهم';
        $messageRegionWithPlaceholder = [];
        $Date = Carbon::now('Asia/Riyadh')->subMonthsNoOverflow(1)->startOfMonth()->toDateString();
        foreach ($duplicatesWithName as $region => $count) {
            $messageWithCount = str_replace('?', $count, $message);
            $messageWithRegion = str_replace('!', $region, $messageWithCount);
            // $messageWithDate = $messageWithRegion . ' [منذ تاريخ % لتاريخ اليوم]';
            $messageWithDate = $messageWithRegion ;
            $messageRegionWithPlaceholder[] = str_replace('%', $Date, $messageWithDate);
        }

        return $messageRegionWithPlaceholder;
    }
}


