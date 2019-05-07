<?php

use Faker\Generator as Faker;

$factory->define(App\Movie::class, function (Faker $faker) {
    return [
    	'mID' 		=> $faker->unique()->numberBetween(120,1000),
    	'title'		=> $faker->realText(50),
    	'year'		=> $faker->unique()->numberBetween(1900,2019),
    	'director'	=> $faker->name
        //
    ];
});
