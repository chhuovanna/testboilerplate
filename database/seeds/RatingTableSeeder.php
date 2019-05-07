<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RatingTableSeeder extends Seeder
{
    public static function randdate(){
    	$startdate = new DateTime('2017-01-01');
    	$enddate = new DateTime('2019-05-01');
    	$randdate = mt_rand($startdate->getTimestamp(), $enddate->getTimestamp());
    	return date("Y-m-d",$randdate);
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	

        
        for($i=0; $i<100 ; $i++){
        	$stars = mt_rand(1, 5);
        	$offsetmovie = mt_rand(0, 54);
        	$offsetreviewer = mt_rand(0, 18);
        	

        	$randmovie = DB::table('movie')
                ->skip($offsetmovie)
                ->take(1)
                ->pluck('mID')
                ->first();
        	$randreviewer = DB::table('reviewer')
                ->skip($offsetreviewer)
                ->take(1)
                ->pluck('rID')
                ->first();
        	
        	$randdate = $this->randdate();

        	//echo $randmovie. ' '.$randreviewer .' '. $randdate .' '.$stars;
        	
			DB::table('rating')->insert([
            'mID' 			=> $randmovie,
            'rID' 			=> $randreviewer,
            'stars'			=> $stars,
            'ratingDate'	=> $randdate
        	]);

        }
    }

    
}
