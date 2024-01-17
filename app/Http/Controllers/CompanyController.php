<?php

namespace App\Http\Controllers;

use App\Models\company;
use App\Http\Requests\StorecompanyRequest;
use App\Http\Requests\UpdatecompanyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class CompanyController extends Controller
{
    public function addCompany(Request $request)
    {
        $request->validate([
            'name_company' => 'required|unique:company|max:255',
        ]);
        $path_logo = $request->file('path_logo')->store('companiesLogo');
        $data = $request->except('path_logo'); // Exclude the file from the general data
        $data['path_logo'] = $path_logo;

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

        // Process the file upload if a new file is provided
        if ($request->hasFile('path_logo')) {
            $path_logo = $request->file('path_logo')->store('companiesLogo');
            $company->path_logo = $path_logo;
        }

        // Update other fields if needed
        $company->fill($request->except('path_logo'));

        $company->save();

        return response()->json(['message' => 'Company updated successfully']);
    }
}
