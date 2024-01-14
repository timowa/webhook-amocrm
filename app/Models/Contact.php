<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{

    protected $fillable=['id','name','responsible_user_id','created_user_id','company_name','custom_fields'];

    protected $casts = ['custom_fields'=>'array','responsible_user_id'=>'integer'];

    public function user(){
        return $this->hasOne(User::class,'id','responsible_user_id');
    }
}
