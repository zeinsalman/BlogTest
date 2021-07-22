<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PostComment extends Model
{
    public  function User(){
        return $this->belongsTo('App\User' ,'user_id');
    }
    public  function Post(){
        return $this->belongsTo('App\Post' ,'post_id');
    }
}
