<?php

use Illuminate\Database\Seeder;

class ReviewerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       	factory(App\Reviewer::class, 80)->create();
    }
}
