<?php

namespace App\Http\Controllers;

use App\Models\task;
use App\Http\Requests\StoretaskRequest;
use App\Http\Requests\UpdatetaskRequest;
use App\Models\client_invoice;

class TaskController extends Controller
{
    public function getinvoiceTask()
    {
        
        return task::invoiceTask(task::find(1)->invoice_id);
    }
}
