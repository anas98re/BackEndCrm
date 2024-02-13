<?php

namespace App\Http\Controllers;

use App\Models\agent;
use App\Http\Requests\StoreagentRequest;
use App\Http\Requests\UpdateagentRequest;
use App\Models\client_invoice;
use App\Services\AgentSrevices;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller
{
    private $MyService;
    public function __construct(AgentSrevices $myService)
    {
        $this->MyService = $myService;
    }
    public function getAgentClints($id)
    {
        $ClientIds = client_invoice::where('fk_agent', $id)->pluck('fk_idClient');
        $clientsInfo = $this->MyService->getAgentClintsService($ClientIds);
        return $this->sendResponse($clientsInfo, 'Done');
    }

    public function getAgentInvoices($id)
    {
        $invoiceIds = client_invoice::where('fk_agent', $id)->pluck('id_invoice');
        $invoiceInfo = $this->MyService->getAgentInvoicesService($invoiceIds);
        return $this->sendResponse($invoiceInfo, 'Done');
    }
}
