<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    public static function getMoviesWithThumbnail($offset=0){
        $movies = Movie::select(['movie.mID', 'title', DB::raw('if(isnull(director), "Unknown", director) as director'), 'year', 'image.file_name', 'image.location'])
        ->leftJoin('image','thumbnail_id', '=', 'image.image_id')->orderBy('movie.mID','desc')->offset($offset)
                ->limit(20)->get();

        return $movies;

    }

}
