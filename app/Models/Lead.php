<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{

    public $textMessageUpdate  = [
        'name'=>'Название сделки изменено: ',
        'price'=>'Цена сделки изменены: ',
        'responsible_user_id'=>'Ответственным назначен: ',
        'updated_at'=>'Дата изменения: '
    ];
    public $textMessageCreate = [
        'name'=>'Имя: ',
        'responsible_user_id'=>'Ответственный: ',
        'price'=>'Цена: ',
        'updated_at'=>'Дата создания: '
    ];
    protected $fillable=['name','price','id','responsible_user_id'];
    protected $casts = ['custom_fields'=>'array','responsible_user_id'=>'integer'];

    public function getUpdateText(){
        return $this->textMessageUpdate;
    }
    public function getCreateText(){
        return $this->textMessageCreate;
    }
}
