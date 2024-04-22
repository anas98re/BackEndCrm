<?php

use App\Models\client_comment;
use App\Models\client_invoice;
use App\Models\clients;
use App\Models\files_invoice;
use App\Models\privg_level_user;
use App\Models\user_maincity;
use App\Models\user_token;
use App\Models\users;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

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


function crudMultiInvoiceFiles($data, $invoice_id, $service)
{
    $invoice = client_invoice::query()->where('id_invoice', $invoice_id)->first();
    if(key_exists('file', $data))
    {
        $filsHandled = $service->storeFile($data['file'], 'invoices');
        if(!str($invoice->image_record)->isEmpty())
        {
            Storage::delete('public/'.$invoice->image_record);
        }
        $invoice->update(['image_record' => $filsHandled]);
    }
    if(key_exists('logo', $data))
    {
        $filsHandled = $service->storeThumbnail($data['logo'], 'logo_client', 200);;
        if(!str($invoice->imagelogo)->isEmpty())
        {
            // dd(Storage::delete(str($invoice->imagelogo)->after('storage/')));
            Storage::delete('public/'.$invoice->imagelogo);
        }
        $invoice->update(['imagelogo' => $filsHandled]);
    }
    if(key_exists('file_to_delete', $data))
    {
        foreach($data['file_to_delete'] as $file_id)
        {
            $fileInvoice = files_invoice::where('id', $file_id)->first();
            if (!is_null($fileInvoice)) {
                Storage::delete('public/'.$fileInvoice->file_attach_invoice);
                $fileInvoice->delete();
            }
        }
    }
    if(key_exists('file_to_attach', $data))
    {
        foreach($data['file_to_attach'] as $file)
        {
            $filsHandled = $service->storeFile($file, 'invoices');
            $fileInvoice = files_invoice::create([
                'fk_invoice' => $invoice->id_invoice,
                'file_attach_invoice' => $filsHandled,
            ]);
        }
    }

    return $invoice->refresh();
}
