<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class PrintNodeService
{
    public function printNode(array $data){
        logger($data);
        $access_token = json_decode(Storage::disk('local')->get('access_token.json'),JSON_OBJECT_AS_ARRAY)['access_token'];
        if(is_array($data['id'])){
            foreach($data['id'] as $id){
                $response[] = $this->sendNode($id,$data['message'],$access_token);
            }
        }else{
            $response = $this->sendNode($data['id'],$data['message'],$access_token);

        }
        logger($response);
    }

    public function sendNode($id,$message,$access_token){
        return Http::withHeaders([
            'Authorization' => 'Bearer '.$access_token,
            'Content-Type'=> 'application/json'
        ])->post('https://'.env('AMOCRM_SUBDOMAIN').'.amocrm.ru/api/v4/leads/'.$id.'/notes',
            [
                ['note_type'=>'common',
                    'params'=>[
                        'text'=>$message
                    ]]
            ]);
    }
}
