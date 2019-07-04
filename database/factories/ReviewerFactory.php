<?php

use Faker\Generator as Faker;

$factory->define(App\Reviewer::class, function (Faker $faker) {
    return [
    	'rID' 		=> $faker->unique()->numberBetween(210,300),
    	'name'		=> $faker->name
        //
    ];
});
