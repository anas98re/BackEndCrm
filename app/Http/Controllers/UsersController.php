<?php

namespace App\Http\Controllers;

use App\Models\users;
use App\Http\Requests\StoreusersRequest;
use App\Http\Requests\UpdateusersRequest;
use App\Http\Resources\UserResource;

class UsersController extends Controller
{
    public function getCurrentUser()
    {
        $currentUser = auth('sanctum')->user();
        if(!$currentUser){
            return $this->Unauthenticated(null);
        }
        $userID = $currentUser?->id_user;
        $user = users::with([
            'country', 'regions', 'level', 'managements', 'privileges', 'mainCity'
        ])
            ->where('id_user', $userID)
            ->first();

        $data = new UserResource($user);
        return $this->sendSucssas($data);
    }
}
