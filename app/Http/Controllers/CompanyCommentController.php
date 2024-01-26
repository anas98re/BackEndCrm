<?php

namespace App\Http\Controllers;

use App\Models\company_comment;
use App\Http\Requests\Storecompany_commentRequest;
use App\Http\Requests\Updatecompany_commentRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CompanyCommentController extends Controller
{
    public function addCommentToCompany(Request $request, $fk_company)
    {
        $companyComment = new company_comment();

        $companyComment->fk_user = $request->id_user;
        $companyComment->fk_company = $fk_company;
        $companyComment->content = $request->content;
        $companyComment->date_comment = Carbon::now('Asia/Riyadh');

        $companyComment->save();
        return $this->sendResponse($companyComment, 'done');
    }
    public function getCommentsViaCompanyId($companyId)
    {
        $companyComments = company_comment::where('fk_company', $companyId)
            ->orderBy('date_comment', 'desc')->get();
        return $this->sendResponse($companyComments, 'done');
    }

}
