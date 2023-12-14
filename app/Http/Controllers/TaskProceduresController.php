<?php

namespace App\Http\Controllers;

use App\Models\taskStatus;
use App\Http\Requests\StoretaskStatusRequest;
use App\Http\Requests\UpdatetaskStatusRequest;
use App\Models\task;
use App\Models\users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskProceduresController extends Controller
{
    public function addTaskToApproveAdminAfterAddInvoice(Request $request)
    {
        try {
            DB::beginTransaction();

            $assigned_to = users::where('fk_regoin', $request->fk_regoin)
                ->where('type_level', 14)->first();

            $task = new task();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->invoice_id = $request->invoice_id;
            $task->public_Type = $request->public_Type;
            $task->assigend_department_from  = 2;
            $task->assigned_to  = $assigned_to->id_user;
            $task->save();

            DB::commit();
            return $task;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }
}
