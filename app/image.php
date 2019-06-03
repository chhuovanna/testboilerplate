<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    //
    protected $table ='image';
    protected $primaryKey = 'image_id';
    public function movie(){
    	return $this->belongsTo('App\movie','mID', 'mID');
    }

    public function thumbnail1(){
    	return $this->hasOne('App\movie','thumbnail_id','image_id');
    }

}
