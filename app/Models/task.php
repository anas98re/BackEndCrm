<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_by',
        'assigned_to',
        'client_id',
        'invoice_id',
        'group_id',
        'public_Type',
        'start_date',
        'deadline',
        'hours',
        'completion_percentage',
        'recurring',
        'recurring_type',
        'Number_Of_Recurring'
    ];

    public function taskStatuses()
    {
        return $this->belongsToMany(TaskStatus::class, 'statuse_task_fraction', 'task_id', 'task_statuse_id');
    }

    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id_user');
    }

    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id_user');
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
        ->leftJoin('task_statuses', 'statuse_task_fraction.task_statuse_id', '=', 'task_statuses.id');

        $filters = [
            'status_name' => ['task_statuses.name', '='],
            'id' => ['tasks.id', '='],
            'assigned_by' => ['assigned_by', '='],
            'assigned_to' => ['assigned_to', '='],
            'created_by' => ['created_by', '='],
            'date_time_created' => ['dateTimeCreated', '='],
            'start_date_from' => ['start_date', '>='],
            'start_date_to' => ['start_date', '<='],
        ];

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
                } else {
                    $tasks->where($column, $operator, $value);
                }
            }
        }

        $tasks = $tasks->get();

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
        return $this->belongsTo(tsks_group::class, 'group_id');
    }
    public function Clients()
    {
        return $this->belongsTo(clients::class, 'client_id', 'id_clients');
    }
    public function invoices()
    {
        return $this->belongsTo(client_invoice::class, 'invoice_id', 'id_invoice');
    }
}
