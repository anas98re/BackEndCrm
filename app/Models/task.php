<?php

namespace App\Models;

use App\Traits\Loggable;
use App\Traits\LoggableTask;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Http\Request;

class task extends Model
{
    use HasFactory, Loggable;

    protected $fillable = [
        'title',
        'description',
        'created_by',
        'assigned_by',
        'assigned_to',
        'client_id',
        'invoice_id',
        'group_id',
        'fk_region',
        'assigend_department_from',
        'assigend_department_to',
        'assigend_region_from',
        'assigend_region_to',
        'assignment_type_from',
        'assignment_type_to',
        'public_Type',
        'main_type_task',
        'recive_date',
        'start_date',
        'deadline',
        'hours',
        'completion_percentage',
        'recurring',
        'dateTimeCreated',
        'recurring_type',
        'Number_Of_Recurring',
        'id_communication',
        'actual_delivery_date'
    ];


    public function taskStatuses()
    {
        return $this->belongsToMany(
            taskStatus::class,
            'statuse_task_fraction',
            'task_id',
            'task_statuse_id'
        )
            ->withPivot('changed_by')
            ->join('users', 'statuse_task_fraction.changed_by', '=', 'users.id_user')
            ->select('task_statuses.*', 'users.nameUser as name_user');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id_user')
            ->select('id_user', 'nameUser', 'img_image', 'fk_regoin', 'type_administration')
            ->join('regoin', 'users.fk_regoin', '=', 'regoin.id_regoin')
            ->join('managements', 'users.type_administration', '=', 'managements.idmange')
            ->select(
                'users.id_user',
                'users.nameUser',
                'users.img_image',
                'users.fk_regoin',
                'users.type_administration',
                'regoin.name_regoin',
                'managements.name_mange'
            );
    }

    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id_user')
            ->join('regoin', 'users.fk_regoin', '=', 'regoin.id_regoin')
            ->join('managements', 'users.type_administration', '=', 'managements.idmange')
            ->select(
                'users.id_user',
                'users.nameUser',
                'users.img_image',
                'users.fk_regoin',
                'users.type_administration',
                'regoin.name_regoin',
                'managements.name_mange'
            );
    }

    public function createByUser()
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    public function managements()
    {
        return $this->belongsTo(managements::class, 'assigend_department_to', 'idmange');
    }

    public function managementsFrom()
    {
        return $this->belongsTo(managements::class, 'assigend_department_from', 'idmange');
    }

    public function regions()
    {
        return $this->belongsTo(regoin::class, 'fk_region', 'id_regoin');
    }

    public function regionsTo()
    {
        return $this->belongsTo(regoin::class, 'assigend_region_to', 'id_regoin');
    }

    public function regionsFrom()
    {
        return $this->belongsTo(regoin::class, 'assigend_region_from', 'id_regoin');
    }

    public function communicationClient()
    {
        return $this->belongsTo(client_communication::class, 'id_communication', 'id_communication');
    }

    public function Filter($filters)
    {
        // return $this->taskStatuses();
        $query = $this->with(['taskStatuses', 'assignedByUser', 'assignedToUser']);

        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'status_name':
                    $query->whereHas('taskStatuses', function ($subQuery) use ($value) {
                        $subQuery->where('name', $value);
                    });
                    break;
                case 'id':
                    $query->where('id', '=', $value);
                    break;
                case 'assigned_by':
                    $query->where('assigned_by', '=', $value);
                    break;
                case 'assigned_to':
                    $query->where('assigned_to', '=', $value);
                    break;
                case 'created_by':
                    $query->where($key, '=', $value);
                    break;
                case 'date_time_created':
                    $query->where('dateTimeCreated', '=', $value);
                    break;
                case 'start_date_from':
                    // $query->where('start_date', '>=', $value);
                    break;
                case 'start_date_to':
                    // $query->where('start_date', '<=', $value);
                    break;
                default:
                    break;
            }
        }

        return $query->get();
    }

    public function filterTaskesByAll($request)
    {
        $tasks = Task::with([
            'taskStatuses', 'assignedByUser',
            'assignedToUser', 'taskGroup',
            'Clients', 'invoices'
        ])
            ->leftJoin('statuse_task_fraction', 'tasks.id', '=', 'statuse_task_fraction.task_id')
            ->leftJoin('task_statuses', 'statuse_task_fraction.task_statuse_id', '=', 'task_statuses.id')
            ->leftJoin('clients', 'tasks.client_id', '=', 'clients.id_clients');

        $filters = [
            'status_name' => ['task_statuses.name', '='],
            'id' => ['tasks.id', '='],
            'assigend_department_from' => ['assigend_department_from', '='],
            'assigend_department_to' => ['assigend_department_to', '='],
            'assigend_region_from' => ['assigend_region_from', '='],
            'assigend_region_to' => ['assigend_region_to', '='],
            'assigned_by' => ['assigned_by', '='],
            'assigned_to' => ['assigned_to', '='],
            'created_by' => ['created_by', '='],
            'date_time_created' => ['dateTimeCreated', '='],
            'start_date_from' => ['start_date', '>='],
            'start_date_to' => ['start_date', '<='],
            'name_enterprise' => ['clients.name_enterprise', '='],
            'mytasks' => ['assigned_by_or_assigned_to', '='],
            'mydepartment' => ['assigend_department_from', '='],
            'mybranch' => ['assigend_region_from_or_assigend_region_to', '='],
        ];

        // $searchTerm = $request->input('name_enterprise');
        // if (!empty($searchTerm)) {
        //     $tasks->orderByRaw("SOUNDEX(clients.name_enterprise) = SOUNDEX('$searchTerm') desc");
        // }

        foreach ($filters as $key => $conditions) {
            if ($request->has($key) && !empty($request->input($key))) {
                $column = $conditions[0];
                $operator = $conditions[1];
                $value = $request->input($key);

                if ($key === 'status_name') {
                    $tasks->where($column, $operator, $value);
                } elseif ($key === 'start_date_from') {
                    $tasks->whereDate($column, $operator, $value);
                } elseif ($key === 'start_date_to') {
                    $tasks->whereDate($column, $operator, $value);
                } elseif ($key === 'mytasks') {
                    $tasks->where(function ($query) use ($value) {
                        $query->where('assigned_by', $value)
                            ->orWhere('assigned_to', $value);
                    });
                } elseif ($key === 'mydepartment') {
                    $tasks->where(function ($query) use ($value) {
                        $users = users::where('type_administration', $value)->get();
                        $userRegionTask = $users->pluck('id_user')->toArray();

                        $query->where('assigend_department_from', $value)
                            ->orWhere('assigend_department_to', $value)
                            ->orwhereIn('assigned_by', $userRegionTask)
                            ->orWhereIn('assigned_to', $userRegionTask);
                    });
                } elseif ($key === 'mybranch') {
                    $tasks->where(function ($query) use ($value) {
                        $users = users::where('fk_regoin', $value)->get();
                        $userRegionTask = $users->pluck('id_user')->toArray();

                        $query->where('assigend_region_from', $value)
                            ->orWhere('assigend_region_to', $value)
                            ->orwhereIn('assigned_by', $userRegionTask)
                            ->orWhereIn('assigned_to', $userRegionTask);
                    });
                } else {
                    $tasks->where($column, $operator, $value);
                }
            }
        }

        $tasks = $tasks->orderBy('tasks.id', 'desc')->get();

        return ($tasks ? $tasks : 'NotFound');
    }

    // ...

    public static function invoiceTask($invoice_id)
    {
        return client_invoice::where('id_invoice', $invoice_id)->first();
    }

    public function taskStatus()
    {
        return $this->belongsTo(TaskStatus::class, 'task_statuse_id');
    }

    public function taskGroup()
    {
        return $this->belongsTo(tsks_group::class, 'group_id')
            ->select('id', 'groupName');;
    }

    public function Clients()
    {
        return $this->belongsTo(clients::class, 'client_id', 'id_clients')
            ->select('id_clients', 'name_enterprise', 'ismarketing');
    }

    public function invoices()
    {
        return $this->belongsTo(client_invoice::class, 'invoice_id', 'id_invoice')
            ->select('id_invoice', 'stateclient');
    }

    public function comments()
    {
    }
}

