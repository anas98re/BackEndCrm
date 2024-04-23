<?php

namespace App\Http\Controllers;

use App\Exports\ClientExport;
use App\Models\task;
use App\Http\Requests\StoretaskRequest;
use App\Http\Requests\TaskManagementRequests\GroupRequest;

use App\Services\TaskManangement\GroupService;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function importClient()
    {
        try
        {
            return Excel::download(new ClientExport, 'clients.xlsx');
        }
        catch(Exception $e)
        {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
}
