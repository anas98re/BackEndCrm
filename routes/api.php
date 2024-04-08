<?php

use App\Http\Controllers\AgentCommentController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ClientCommentMentionController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\ClientsDateController;
use App\Http\Controllers\ClientsUpdateReportController;
use App\Http\Controllers\CommentParticipateController;
use App\Http\Controllers\CompanyCommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\FilesInvoiceController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImportantLinkController;
use App\Http\Controllers\InvoicesUpdateReportController;
use App\Http\Controllers\MaincityController;
use App\Http\Controllers\NotifiactionController;
use App\Http\Controllers\ParticipateController;
use App\Http\Controllers\PaymentDetailController;
use App\Http\Controllers\PrivgLevelUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SeriesInvoiceacceptController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskProceduresController;
use App\Http\Controllers\TicketsController;
use App\Http\Controllers\UpdatesReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// use

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('register', [RegisterController::class, 'register']);

Route::post('insertPrivelgeToAllLevel', [PrivgLevelUserController::class, 'insertPrivelgeToAllLevel']);
Route::post('addTaskToApproveAdminAfterAddInvoice', [TaskProceduresController::class, 'addTaskToApproveAdminAfterAddInvoice']);
Route::post('closeTaskApproveAdminAfterAddInvoice', [TaskProceduresController::class, 'closeTaskApproveAdminAfterAddInvoice']);
Route::post('closeWelcomeTaskAfterUpdateCommunication', [TaskProceduresController::class, 'closeWelcomeTaskAfterUpdateCommunication']);
Route::post('afterInstallClient', [TaskProceduresController::class, 'afterInstallClient']);
Route::post('closeTaskAfterInstallClient', [TaskProceduresController::class, 'closeTaskAfterInstallClient']);
Route::post('addTaskApproveFinanceAfterApproveSales', [TaskProceduresController::class, 'addTaskApproveFinanceAfterApproveSales']);
Route::post('closeTaskApproveFinanceAfterApproveSales', [TaskProceduresController::class, 'closeTaskApproveFinanceAfterApproveSales']);
Route::post('addTaskAddVisitDateAfterApproveInvoice', [TaskProceduresController::class, 'addTaskAddVisitDateAfterApproveInvoice']);
Route::post('closeTaskAddVisitDateAfterApproveInvoice', [TaskProceduresController::class, 'closeTaskAddVisitDateAfterApproveInvoice']);
Route::post('closeTaskafterCommunicateWithClient', [TaskProceduresController::class, 'closeTaskafterCommunicateWithClient']);
Route::post('addTaskafterAddPaymentToTheInvoiceForReviewInvoice', [TaskProceduresController::class, 'addTaskafterAddPaymentToTheInvoiceForReviewInvoice']);
Route::post('addTaskWhenThereIsNoUpdateToTheLatestClientUpdatesFor5Days', [TaskProceduresController::class, 'addTaskWhenThereIsNoUpdateToTheLatestClientUpdatesFor5Days']);
//clients comments mentions
Route::post('addCommentClientMention', [ClientCommentMentionController::class, 'addCommentClientMention']);
//cllients Invoices
Route::post('storageInvoicesUpdates', [UpdatesReportController::class, 'storageInvoicesUpdates']);
Route::post('addInvoicesUpdateReport', [UpdatesReportController::class, 'addInvoicesUpdateReport']);
Route::post('addInvoiceProductReport', [UpdatesReportController::class, 'addInvoiceProductReport']);
Route::post('reportDeletedIdsFillesInvoice', [UpdatesReportController::class, 'reportDeletedIdsFillesInvoice']);
//reports
Route::post('addUserUpdateReport', [UpdatesReportController::class, 'addUserUpdateReport']);
//cllients
Route::post('storageClientsUpdates', [UpdatesReportController::class, 'storageClientsUpdates']);
Route::post('storageClientCommunicationUpdates', [UpdatesReportController::class, 'storageClientCommunicationUpdates']);

Route::post('getUsersByTypeAdministrationAndRegion', [RegisterController::class, 'getUsersByTypeAdministrationAndRegion']);

//PaymentDetails
Route::post('createPaymentDetails', [PaymentDetailController::class, 'createPaymentDetails']);
Route::get('getPaymaentsViaInvoiceId/{id}', [PaymentDetailController::class, 'getPaymaentsViaInvoiceId']);

// Route::middleware(['auth:sanctum', 'knowCurrentUser'])->group(function () {
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('test', [RegisterController::class, 'test']);

    //Tasks
    Route::post('addTask', [TaskController::class, 'addTask']);
    Route::post('editTask/{id}', [TaskController::class, 'editTask']);
    Route::post('addGroup', [GroupController::class, 'addGroup']);
    Route::post('editGroup/{id}', [GroupController::class, 'editGroup']);
    Route::post('assignTaskToEmployee/{id}', [TaskController::class, 'assignTaskToEmployee']);
    Route::post('changeStatuseTask/{id}', [TaskController::class, 'changeStatuseTask']);
    Route::get('viewTasksByIdAssigned/{id}', [TaskController::class, 'viewTasksByIdAssigned']);
    Route::get('viewTaskByIdTask/{id}', [TaskController::class, 'viewTaskByIdTask']);
    Route::get('viewAllTasksByStatus/{statusName}', [TaskController::class, 'viewAllTasksByStatus']);
    Route::get('viewAllTasks', [TaskController::class, 'viewAllTasks']);
    Route::post('viewAllTasksByDateTimeCrated', [TaskController::class, 'viewAllTasksByDateTimeCrated']);
    Route::post('filterTaskesByAll', [TaskController::class, 'filterTaskesByAll']);
    Route::post('changeTaskGroup/{id}', [TaskController::class, 'changeTaskGroup']);
    Route::post('addAttachmentsToTask/{id}', [TaskController::class, 'addAttachmentsToTask']);
    Route::post('addCommentToTask/{id}', [TaskController::class, 'addCommentToTask']);
    Route::get('viewCommentsByTaskId/{id}', [TaskController::class, 'viewCommentsByTaskId']);
    Route::get('getGroupsInfo', [TaskController::class, 'getGroupsInfo']);


    //Clients
    Route::get('getClientByID/{id}', [ClientsController::class, 'getClientByID']);
    Route::post('editClientByTypeClient/{id_clients}', [ClientsController::class, 'editClientByTypeClient']);
    Route::post('clientAppproveAdmin/{id_clients}', [ClientsController::class, 'appproveAdmin']);
    Route::post('transformClientsFromMarketingIfOverrideLimit8Days', [ClientsController::class, 'transformClientsFromMarketingIfOverrideLimit8Days']);
    Route::post('addClient', [ClientsController::class, 'addClient']);
    Route::post('updateClient/{id}', [ClientsController::class, 'updateClient']);
    Route::post('SimilarClientsNames', [ClientsController::class, 'SimilarClientsNames']);
    Route::post('convertClientsFromAnEmployeeToEmployee', [ClientsController::class, 'convertClientsFromAnEmployeeToEmployee']);
    Route::post('sendStactictesConvretClientsToEmail', [ClientsController::class, 'sendStactictesConvretClientsToEmail']);
    Route::get('editDatePriceDataToCorrectFormatt', [ClientsController::class, 'editDatePriceDataToCorrectFormatt']);
    Route::get('getTransferClientsWithPrivileges', [ClientsController::class, 'getTransferClientsWithPrivileges']);
    Route::post('transferClient/{id}', [ClientsController::class, 'transferClient']);
    Route::post('approveOrRefuseTransferClient/{id}', [ClientsController::class, 'approveOrRefuseTransferClient']);
    //Clients Participate 
    Route::get('getParticipateClints/{id}', [ParticipateController::class, 'getParticipateClints']);
    Route::get('getParticipateInvoices/{id}', [ParticipateController::class, 'getParticipateInvoices']);
    Route::post('addCommentParticipate', [CommentParticipateController::class, 'addCommentParticipate']);
    Route::get('getParticipateComments/{id}', [CommentParticipateController::class, 'getParticipateComments']);
    //Clients Agents
    Route::get('getAgentClints/{id}', [AgentController::class, 'getAgentClints']);
    Route::get('getAgentInvoices/{id}', [AgentController::class, 'getAgentInvoices']);
    Route::post('addCommentAgent', [AgentCommentController::class, 'addCommentAgent']);
    Route::get('getAgentComments/{id}', [AgentCommentController::class, 'getAgentComments']);
    //clients Date
    Route::post('rescheduleOrCancelVisitClient/{idclients_date}', [ClientsDateController::class, 'rescheduleOrCancelVisitClient']);
    Route::get('getDateVisitAgent/{agentId}', [ClientsDateController::class, 'getDateVisitAgentFromQuery']);
    Route::post('updateStatusForVisit/{date_id}', [ClientsDateController::class, 'updateStatusForVisit']);
    //Cities
    Route::post('getCitiesFromMainCitiesIds', [MaincityController::class, 'getCitiesFromMainCitiesIds']);
    //cllients Excel
    Route::post('importClints', [ClientsController::class, 'importClints']);
    Route::post('importAnotherClints', [ClientsController::class, 'importAnotherClints']);


    //invoices
    Route::post('InvoiceFiles', [FilesInvoiceController::class, 'InvoiceFiles']);
    Route::get('getFilesInvoices', [FilesInvoiceController::class, 'getFilesInvoices']);
    Route::post('updateInvoiceFile/{id}', [FilesInvoiceController::class, 'updateInvoiceFile']);
    Route::post('deleteInvoiceFile/{id}', [FilesInvoiceController::class, 'deleteInvoiceFile']);
    //series invoice
    Route::get('getSeriesInvoiceAll', [SeriesInvoiceacceptController::class, 'getSeriesInvoiceAll']);


    //company ...
    Route::post('addCommentToCompany/{fk_company}', [CompanyCommentController::class, 'addCommentToCompany']);
    Route::get('getCommentsViaCompanyId/{companyId}', [CompanyCommentController::class, 'getCommentsViaCompanyId']);
    Route::post('addCompany', [CompanyController::class, 'addCompany']);
    Route::post('updateCompany/{companyId}', [CompanyController::class, 'updateCompany']);
    //company Excel
    Route::post('importCompanyComment', [CompanyCommentController::class, 'importCompanyComment']);

    //Links
    Route::post('addLink', [ImportantLinkController::class, 'addLink']);
    Route::post('editLink/{id}', [ImportantLinkController::class, 'editLink']);
    Route::get('getAllLink', [ImportantLinkController::class, 'getAllLink']);
    Route::post('deleteLink/{id}', [ImportantLinkController::class, 'deleteLink']);

    // links Excel
    Route::get('export', [ImportantLinkController::class, 'export']);
    Route::post('import', [ImportantLinkController::class, 'import']);

    //subcategories_ticketImport Excel
    Route::post('importCategoriesTicket', [TicketsController::class, 'importCategoriesTicket']);
    Route::post('importSubCategoriesTicket', [TicketsController::class, 'importSubCategoriesTicket']);
    Route::get('getSubCategoriesTicket', [TicketsController::class, 'getSubCategoriesTicket']);
    Route::get('getCategoriesTicket', [TicketsController::class, 'getCategoriesTicket']);

    //Tickets
    Route::post('addTicket', [TicketsController::class, 'addTicket']);
    Route::post('editTicketType/{id}', [TicketsController::class, 'editTicketType']);
    Route::get('getTicketById/{id}', [TicketsController::class, 'getTicketById']);
    Route::get('getTickets', [TicketsController::class, 'getTickets']);
    Route::post('TransferTicket/{id}', [TicketsController::class, 'TransferTicket']);
    Route::get('reopenReportTickets', [TicketsController::class, 'reopenReport']);
    Route::post('transferTicketsTable', [TicketsController::class, 'transferTable']);

});

Route::post('addEmailFromAdmin', [RegisterController::class, 'addEmailFromAdmin']);
Route::get('getCurrentUser', [RegisterController::class, 'getCurrentUser']);
Route::get('getHashToken', [RegisterController::class, 'getHashToken']);
Route::post('checkEmail', [RegisterController::class, 'checkEmail']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('isTokenAuthenticated', [RegisterController::class, 'isTokenAuthenticated']);
Route::post('updatePermissions', [PrivgLevelUserController::class, 'updatePermissions']);
Route::post('sendupdatePermissionsReportToEmail', [PrivgLevelUserController::class, 'sendupdatePermissionsReportToEmail']);


Route::get('getinvoiceTask', [TaskController::class, 'getinvoiceTask']);
Route::get('testNotify', [NotifiactionController::class, 'testNotify']);

//opt/cpanel/ea-php81/root/bin/php /usr/local/bin/composer update
