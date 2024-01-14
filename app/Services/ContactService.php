<?php

namespace App\Services;

use App\Jobs\UsersJob;
use App\Models\Contact;
use App\Models\User;

class ContactService
{
    public function add(array $data){
        //проверяем существует ли запись с таким id
        if(Contact::find($data['id'])!=null)
            throw new \Exception('Контакт с таким id уже существует');
        //создаем в бд запись контакта по входным данным
        $contact = Contact::create($data);
        logger('Создание контакта: '.$contact);
        //формируем текст для примечания
        $message = $this->generateCreateText($contact);
        $id = array_keys($data['linked_leads_id']);
        logger($id);
        return ['id'=>$id,'message'=>$message];
    }



    public function update(array $data){
        $contact = Contact::find($data['id']);
        if($contact==null)
            throw new \Exception('Контакт с таким id не существует');
        $oldData = $contact->getAttributes();
        $contact->update($data);
        $message = $this->generateUpdateText($contact->getChanges(),$oldData);
        $id = array_keys($data['linked_leads_id']);
        logger($id);
        return ['id'=>$id,'message'=>$message];
    }


    public function generateCreateText(Contact $contact){
        $text = 'Создан контакт #'.$contact->id.'. ';
        foreach ($contact->getAttributes() as $attributeKey => $attributeValue){
            switch ($attributeKey){
                case 'name':
                    $text.='Имя: '.$attributeValue.'. '; break;
                case 'responsible_user_id':
                    $user = User::find($attributeValue);
                    if($user==null) {
                        $refreshUsers = new UsersJob();
                        $refreshUsers->handle();
                    }
                        $name = $user->name;
                    $text.='Ответственный: '.$name.'. ';break;
                case 'created_user_id':
                    $user = User::find($attributeValue);
                    if($user==null) {
                        $refreshUsers = new UsersJob();
                        $refreshUsers->handle();
                    }
                    $name = $user->name;
                    if($contact->responsible_user_id != $contact->created_user_id)
                        $text.='Создатель: '.$name.'. ';
                    break;
                case 'company_name':
                    $text.='Компания: '.$attributeValue.'. ';break;
                case 'custom_fields':
                    //проверяем наличие дополнительных полей
                    if($attributeValue!=null){
                        $text.='Доп. данные: ';
                        $attributeValue = json_decode($attributeValue,JSON_OBJECT_AS_ARRAY);
                        //Перечисляем данные с доп. полей в виде Название_поля: Значение1, Значение2...;
                        foreach ($attributeValue as $field){
                            $text.=$field['name'].': ';

                            //Перебираем все значения поля
                            foreach ($field['values'] as $fieldValue){
                                $text.= $fieldValue['value'];

                                //Выбираем какой знак поставить после него
                                if(end($field['values'])==$fieldValue)
                                    $text.='. ';
                                else
                                    $text.=', ';
                            }

                            //выбираем какой знак поставить после очередного поля
                            if(end($attributeValue)==$field)
                                $text.='. ';
                            else
                                $text.='; ';
                        }
                    }
                    break;
                case 'created_at':
                    $text.='Дата создания: '.$attributeValue;break;
            }
        }
        return $text;
    }

    public function generateUpdateText(array $changes,array $oldData){
        $text = 'Контакт #'.$oldData['id'].' обновлен. ';
        $message = false;
        foreach ($changes as $key=>$value){
            switch($key){
                case 'updated_at':
                    $text.='Дата обновления: '.$value;break;
                case 'name':
                    $message = true;
                    $text.='Имя изменено: с'.$oldData[$key].' на '.$value.'. '; break;
                case 'responsible_user_id':
                    $message = true;
                    $oldName = User::find($oldData[$key])->name;
                    $newName = User::find($value)->name;
                    $text.='Ответственный изменен: с'.$oldName.' на '.$newName.'. ';break;
                case 'company_name':
                    $message = true;
                    $text.='Компания изменена с: '.$oldData[$key].'на '.$value.'. ';break;
                case 'custom_fields':
                    $message = true;
                    $newValue = json_decode($value,JSON_OBJECT_AS_ARRAY);
                    $oldValue = json_decode($oldData[$key],JSON_OBJECT_AS_ARRAY);
                    foreach ($newValue as $index => $array){
                        $changed = [];
                        foreach ($array['values'] as $valueIndex => $valueArray){
                            if(array_diff($valueArray,$oldValue[$index]['values'][$valueIndex])!=null);
                                $changed = array_diff($valueArray,$oldValue[$index]['values'][$valueIndex]);
                        }
                        if($changed)
                            $text.=$array['name'].': '.implode(',',$changed);
                    }
                    break;
            }
        }
        if($message)
            return $text;
        return false;
    }
}
