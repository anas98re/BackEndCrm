<?php

namespace App\Services\TaskManangement;

use App\Http\Requests\TaskManagementRequests\GroupRequest;
use App\Http\Requests\TaskManagementRequests\TaskRequest;
use App\Models\tsks_group;
use App\Services\JsonResponeService;
use Illuminate\Support\Facades\DB;


class GroupService extends JsonResponeService
{
    public function addGroup(GroupRequest $request)
    {

        try {
            DB::beginTransaction();

            $tsks_group = new tsks_group();
            $tsks_group->created_by = auth('sanctum')->user()->id_user;
            $tsks_group->groupName = $request->groupName;
            $tsks_group->description = $request->description;
            $tsks_group->save();

            DB::commit();

            return $tsks_group;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
        }
    }
}
