<?php

use Faker\Generator as Faker;

$factory->define(App\Reviewer::class, function (Faker $faker) {
    return [
    	'rID' 		=> $faker->unique()->numberBetween(222,1000),
    	'name'		=> $faker->name
        //
    ];
});
