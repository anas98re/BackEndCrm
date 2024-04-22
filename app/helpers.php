<?php

use App\Models\client_comment;
use App\Models\clients;
use App\Models\privg_level_user;
use App\Models\user_maincity;
use App\Models\user_token;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Support\Collection;

function getIdLevelsByPrivilge($fk_privileg): Collection
{
    return privg_level_user::query()
        ->where('fk_privileg', $fk_privileg)
        ->where('is_check', 1)
        ->get()
        ->pluck('fk_level');
}

function getIdLevelsByPrivilges(array $fk_privilegs): Collection
{
    return privg_level_user::query()
        ->whereIn('fk_privileg', $fk_privilegs)
        ->where('is_check', 1)
        ->get()
        ->pluck('fk_level')
        ->unique();
}

function getIdUsers($fk_regoin, $fk_privileg, $fk_country = null)
{
    $levels = getIdLevelsByPrivilge($fk_privileg);
    if(is_null($fk_country))
        $id_users = users::query()
            ->where(function ($query) use ($levels, $fk_regoin) {
                $query->where('fk_regoin', $fk_regoin)
                    ->whereIn('type_level', $levels);
            })
            ->orWhere(function ($query) use ($levels) {
                $query->where('fk_regoin', 14)
                    ->whereIn('type_level', $levels);
            })
            ->get()
            ->pluck('id_user');
    else
        $id_users = users::query()
            ->where(function ($query) use ($levels, $fk_regoin) {
                $query->where('fk_regoin', $fk_regoin)
                    ->whereIn('type_level', $levels);
            })
            ->orWhere(function ($query) use ($levels, $fk_country) {
                $query->where('fk_regoin', 14)
                    ->where('fk_country', $fk_country)
                    ->whereIn('type_level', $levels);
            })
            ->get()
            ->pluck('id_user');

    return $id_users;
}

function getIdUsersRegoin($fkcountry,$fk_privileg,$fkclient )
{
    $fkmaincity=  clients::where('id_clients', $fkclient)?->first()?->cityRelation?->fk_maincity;
    $arraylevel = getIdLevelsByPrivilge($fk_privileg);

    return user_maincity::select('users.id_user')
    ->join('users', 'users.id_user', '=', 'user_maincity.fk_user')
    ->where('users.fk_country', $fkcountry)
    ->where('user_maincity.fk_maincity', $fkmaincity)
    ->whereIn('users.type_level', $arraylevel)
    ->get()
    ->pluck('id_user');
}


function addComment($content,$fk_client,$fk_user,$type_comment)
{
    $data['fk_client'] = $fk_client;
    $data['fk_user'] = $fk_user;
    $data['date_comment'] = Carbon::now()->format('Y-m-d H:i:s');
    $data['content'] = $content;
    $data['type_comment'] = $type_comment;

    return client_comment::create($data);
}

function getTokens(Collection $user_ids): Collection
{
    $users = users::whereIn('id_user', $user_ids)->get();
    $tokens = collect();
    foreach($user_ids as $user_id)
    {
        $tokens[] = DB::table('user_token')->where('fkuser', $user_id)
        ->where('token', '!=', null)
        ->latest('date_create')
        ->first()?->token;
    }
    return $tokens->flatten()->filter();
}
