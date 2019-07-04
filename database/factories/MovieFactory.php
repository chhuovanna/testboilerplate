<?php

use Faker\Generator as Faker;

$factory->define(App\Movie::class, function (Faker $faker) {
    return [
    	'mID' 		=> $faker->unique()->numberBetween(212,1000),
    	'title'		=> $faker->realText(50),
    	'year'		=> $faker->numberBetween(1900,2019),
    	'director'	=> $faker->name
        //
    ];
});
