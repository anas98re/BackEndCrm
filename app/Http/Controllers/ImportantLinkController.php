<?php

namespace App\Http\Controllers;

use App\Models\importantLink;
use App\Http\Requests\StoreimportantLinkRequest;
use App\Http\Requests\UpdateimportantLinkRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Exports\ImportantLinksExport;
use App\Imports\ImportantLinksImport;
use Maatwebsite\Excel\Facades\Excel;

class ImportantLinkController extends Controller
{
    public function addLink(Request $request)
    {
        $data = $request->all();
        $data['add_date'] = Carbon::now('Asia/Riyadh');

        $link = importantLink::create($data);
        return $this->sendResponse($link, 'Link created successfully');
    }

    public function editLink(Request $request, $id)
    {
        $data = $request->all();
        $data['edit_date'] = Carbon::now('Asia/Riyadh');

        $link = importantLink::find($id);

        $link->update($data);

        return $this->sendResponse($link, 'Link updated successfully');
    }

    public function getAllLink()
    {
        $links = ImportantLink::orderBy('add_date', 'desc')->get();

        return $this->sendResponse($links, 'These are all links');
    }

    public function deleteLink($id)
    {
        $link = ImportantLink::find($id);
        $link->delete();
        return $this->sendResponse('done', 'link deleted successfully');
    }

    public function export()
    {
        return Excel::download(new ImportantLinksExport, 'important_links.xlsx');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');

        Excel::import(new ImportantLinksImport, $file);

        return $this->sendResponse('success', 'Important links imported successfully.');
    }

}
