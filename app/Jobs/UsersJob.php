<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UsersJob
{
    public function handle(){
        $access_token = json_decode(Storage::disk('local')->get('access_token.json'),JSON_OBJECT_AS_ARRAY)['access_token'];
        $users = Http::withHeaders([
            'Authorization' =>'Bearer '.$access_token,
            'Content-Type'=> 'application/json'
        ])->get('https://'.env('AMOCRM_SUBDOMAIN').'.amocrm.ru/api/v4/users');
        logger($users);
        $list =  $users['_embedded']['users'];
        foreach ($list as $user){
            User::updateOrCreate(
                ['id'=>$user['id']],
                ['name'=>$user['name']]
            );
        }
    }
}
