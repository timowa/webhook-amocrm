<?php

namespace App\Services;

use App\Jobs\UsersJob;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\User;

class LeadService
{

    public function add(array $data){
        //проверяем существует ли запись с таким id
        if(Lead::find($data['id'])!=null)
            throw new \Exception('Сделка с таким id уже существует');
        //создаем в бд запись контакта по входным данным
        $lead = Lead::create($data);

        //формируем текст для примечания
        $message = $this->generateCreateText($lead);
        return ['id'=>$data['id'],'message'=>$message];
    }



    public function update(array $data){
        $lead = Lead::find($data['id']);
        if($lead==null)
            throw new \Exception('Сделки с таким id не существует');
        $oldData = $lead->getAttributes();
        $lead->update($data);
        $message = $this->generateUpdateText($lead->getChanges(),$oldData);
        return ['id'=>$data['id'],'message'=>$message];
    }


    public function generateCreateText(Lead $lead){
        $text = 'Создана сделка #'.$lead->id.'. ';
        foreach ($lead->getAttributes() as $attributeKey => $attributeValue){
            switch ($attributeKey){
                case 'name':
                    $text.='Имя: '.$attributeValue.'. '; break;
                case 'price':
                    $text.='Цена: '.$attributeValue.'. '; break;
                case 'responsible_user_id':
                    $user = User::find($attributeValue);
                    if($user==null) {
                        $refreshUsers = new UsersJob();
                        $refreshUsers->handle();
                    }
                    $name = $user->name;
                    $text.='Ответственный: '.$name.'. ';break;
                case 'created_at':
                    $text.='Дата создания: '.$attributeValue;break;
            }
        }
        return $text;
    }

    public function generateUpdateText(array $changes,array $oldData){
        $text = 'Сделка #'.$oldData['id'].' обновлена. ';
        $message = false;
        foreach ($changes as $key=>$value){
            switch($key){
                case 'updated_at':
                    $message = true;
                    $text.='Дата обновления: '.$value;break;
                case 'name':
                    $message = true;
                    $text.='Имя изменено: с '.$oldData[$key].' на '.$value.'. '; break;
                case 'responsible_user_id':
                    $message = true;
                    $oldName = User::find($oldData[$key])->name;
                    $newName = User::find($value)->name;
                    $text.='Ответственный изменен: с '.$oldName.' на '.$newName.'. ';break;
                case 'price':
                    $message = true;
                    $text.='Бюджет изменен: с '.$oldData[$key].'₽ на '.$value.'₽. ';break;
            }
        }
        if($message)
            return $text;
        return false;
    }
//    public function createLead(array $data){
//        $lead = new Lead();
//        $fillable = $lead->getFillable();
//        $fields = [];
//        foreach ($fillable as $field){
//            if(isset($data[$field]) && $field!='id')
//                $fields[$field]=$data[$field];
//        }
//        $oldContact = Lead::find($data['id']);
//        $lead = Lead::updateOrCreate(
//            ['id'=>$data['id']],
//            $fields
//        );
//        $message = '';
//        if($lead->wasRecentlyCreated){
//            $message = 'Сделка #'.$data['id'].' создана. ';
//            $text = $lead->getCreateText();
//            foreach ($text as $key => $value){
//                if($key=='responsible_user_id'){
//                    if($lead->user == null){
//                        $users = UsersJob::class;
//                        $users->handle();
//                    }
//                    $message.=$value.=$lead->user->name.'. ';
//                }
//                elseif ($lead->{$key}!=null)
//                    $message.=$value.=$lead->{$key}.'. ';
//
//            }
//        }
//        elseif(!$lead->wasRecentlyCreated && $lead->wasChanged()){
//            $message = 'Сделка #'.$data['id'].' обновлена. ';
//            $text = $lead->getUpdateText();
//            foreach ($lead->getChanges() as $key=>$value){
//                if(isset($text[$key]))
//                    if($key=='responsible_user_id'){
//                        if($lead->user == null){
//                            $users = UsersJob::class;
//                            $users->handle();
//                        }
//                        $message.=$text[$key].=$lead->user->name.'. ';
//                        continue;
//                    }
//                if($key=='updated_at'){
//                    $message.=$text[$key].$value.'. ';
//                    continue;
//                }
//                $message.=$text[$key].'с '.$oldContact->{$key}.' на '.$value.'. ';
//
//            }
//        }
//        return ['id'=>$data['id'],'message'=>$message];
//    }
}
