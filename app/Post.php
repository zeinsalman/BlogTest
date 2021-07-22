<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    public  function User(){
        return $this->belongsTo('App\User' ,'user_id')->select('id', 'name' , 'created_at', 'updated_at');
    }
    public function comments(){
        return $this->hasMany('App\PostComment', 'post_id');
    }
}
