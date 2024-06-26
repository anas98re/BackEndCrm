<?php

namespace App\Http\Controllers;

use App\Models\company_comment;
use App\Http\Requests\Storecompany_commentRequest;
use App\Http\Requests\Updatecompany_commentRequest;
use App\Http\Resources\companrCommentResources;
use App\Imports\CompanyImport;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Facades\Excel;


class CompanyCommentController extends Controller
{
    public function addCommentToCompany(Request $request, $fk_company)
    {
        $companyComment = new company_comment();

        $companyComment->fk_user = (int)$request->id_user;
        $companyComment->fk_company = (int)$fk_company;
        $companyComment->content = $request->content;
        $companyComment->date_comment = Carbon::now('Asia/Riyadh');

        $companyComment->save();

        // Retrieve user details
        $user = users::find($request->id_user);

        // Add user details to the comment object
        $companyComment->nameUser = $user->nameUser;
        $companyComment->img_image = $user->img_image ? $user->img_image : '';


        // Return the comment object as response
        return $this->sendResponse($companyComment, 'done');
    }
    public function getCommentsViaCompanyId($companyId)
    {
        $companyComments = company_comment::where('fk_company', $companyId)
            ->with('Users')
            ->orderBy('date_comment', 'desc')
            ->get();
        return $this->sendResponse(companrCommentResources::collection($companyComments), 'These are all comments for this company');
    }

    public function importCompanyComment(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new CompanyImport, $file);

        return $this->sendResponse('success', 'Important company comments imported successfully.');
    }
}
