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
        return $this->belongsTo(TaskGroup::class, 'group_id');
    }
}
