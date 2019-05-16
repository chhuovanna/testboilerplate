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
    	$this->hasMany('App\rating','mID', 'mID');
    }

}
