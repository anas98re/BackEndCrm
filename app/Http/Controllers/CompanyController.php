<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Http\Requests\StorecompanyRequest;
use App\Http\Requests\UpdatecompanyRequest;
use App\Services\CompanySrevices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class CompanyController extends Controller
{
    private $MyService;

    public function __construct(CompanySrevices $MyService)
    {
        $this->MyService = $MyService;
    }
    public function addCompany(Request $request)
    {
        $request->validate([
            'name_company' => 'required|unique:company|max:255',
        ]);
        $generatedPath = $this->MyService->handlingImageName($request->file('path_logo'));

        $data = $request->except('path_logo'); // Exclude the file from the general data
        $data['path_logo'] = $generatedPath;

        company::create($data);
        return response()->json(['message' => 'Company created successfully']);
    }

    public function updateCompany(Request $request, $companyId)
    {
        $request->validate([
            'name_company' => 'unique:company,name_company,' . $companyId . ',' . 'id_Company' . '|max:255',
        ]);

        $company = company::find($companyId);

        if ($company->path_logo) {
            Storage::delete($company->path_logo);
        }

        if ($request->hasFile('path_logo')) {
            $generatedPath = $this->MyService->handlingImageName($request->file('path_logo'));
            $company->path_logo = $generatedPath;
        }

        // Update other fields if needed
        $company->fill($request->except('path_logo'));
        $company->save();
        return response()->json(['message' => 'Company updated successfully']);
    }
}
