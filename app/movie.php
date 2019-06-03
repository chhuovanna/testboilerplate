<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class movie extends Model
{
    //
    protected $table ='movie';
    protected $primaryKey = 'mID';
    public $timestamps = false;
    public function rating(){
    	return $this->hasMany('App\rating','mID', 'mID');
    }

    public function photos(){
    	return $this->hasMany('App\image','mID','mID');
    }

    public function thumbnail(){
    	return $this->belongsTo('App\image','thumbnail_id','image_id');
    }

}
