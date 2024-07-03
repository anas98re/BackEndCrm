<?php

namespace App\Http\Controllers;

use App\Models\maincity;
use App\Http\Requests\StoremaincityRequest;
use App\Http\Requests\UpdatemaincityRequest;
use Illuminate\Http\Request;

class MaincityController extends Controller
{
    public function getCitiesFromMainCitiesIds(Request $request)
    {
        $mainCitiesIds = json_decode($request->mainCitiesIds, true);

        // Ensure $mainCitiesIds is an array
        $mainCitiesIds = is_array($mainCitiesIds) ? $mainCitiesIds : [];

        $mainCities = maincity::whereIn('id_maincity', $mainCitiesIds)->get();

        $cities = [];

        foreach ($mainCities as $mainCity) {
            // Access the cities associated with the main city
            $mainCityCities = $mainCity->cities;

            // Merge the cities into the $cities array
            $cities = array_merge($cities, $mainCityCities->toArray());
        }

        return $this->sendResponse($cities, 'Done');;
    }
}

