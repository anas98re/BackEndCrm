<?php

use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CompanyCommentController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ImportantLinkController;
use App\Http\Controllers\NotifiactionController;
use App\Http\Controllers\PrivgLevelUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskProceduresController;
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


Route::post('getUsersByTypeAdministrationAndRegion', [RegisterController::class, 'getUsersByTypeAdministrationAndRegion']);


Route::middleware(['auth:sanctum', 'knowCurrentUser'])->group(function () {
// Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('test', [RegisterController::class, 'test']);

    //Tasks
    Route::post('addTask', [TaskController::class, 'addTask']);
    Route::post('editTask/{id}', [TaskController::class, 'editTask']);
    Route::post('addGroup', [GroupController::class, 'addGroup']);
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
    Route::post('editClientByTypeClient/{id_clients}', [ClientsController::class, 'editClientByTypeClient']);
    Route::post('clientAppproveAdmin/{id_clients}', [ClientsController::class, 'appproveAdmin']);
    Route::post('transformClientsFromMarketingIfOverrideLimit8Days', [ClientsController::class, 'transformClientsFromMarketingIfOverrideLimit8Days']);
    Route::post('addClient', [ClientsController::class, 'addClient']);
    Route::post('SimilarClientsNames', [ClientsController::class, 'SimilarClientsNames']);
    Route::post('convertClientsFromAnEmployeeToEmployee', [ClientsController::class, 'convertClientsFromAnEmployeeToEmployee']);
    Route::post('sendStactictesConvretClientsToEmail', [ClientsController::class, 'sendStactictesConvretClientsToEmail']);

    //company ...
    Route::post('addCommentToCompany/{fk_company}', [CompanyCommentController::class, 'addCommentToCompany']);
    Route::get('getCommentsViaCompanyId/{companyId}', [CompanyCommentController::class, 'getCommentsViaCompanyId']);
    Route::post('addCompany', [CompanyController::class, 'addCompany']);
    Route::post('updateCompany/{companyId}', [CompanyController::class, 'updateCompany']);

    //Links
    Route::post('addLink', [ImportantLinkController::class, 'addLink']);
    Route::post('editLink/{id}', [ImportantLinkController::class, 'editLink']);
    Route::get('getAllLink', [ImportantLinkController::class, 'getAllLink']);
    Route::post('deleteLink/{id}', [ImportantLinkController::class, 'deleteLink']);
});

Route::post('addEmailFromAdmin', [RegisterController::class, 'addEmailFromAdmin']);
Route::get('getCurrentUser', [RegisterController::class, 'getCurrentUser']);
Route::post('checkEmail', [RegisterController::class, 'checkEmail']);
Route::post('login', [RegisterController::class, 'login']);
Route::post('updatePermissions', [PrivgLevelUserController::class, 'updatePermissions']);
Route::post('sendupdatePermissionsReportToEmail', [PrivgLevelUserController::class, 'sendupdatePermissionsReportToEmail']);


Route::get('getinvoiceTask', [TaskController::class, 'getinvoiceTask']);
Route::get('testNotify', [NotifiactionController::class, 'testNotify']);
