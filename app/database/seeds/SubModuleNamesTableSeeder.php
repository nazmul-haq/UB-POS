<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SubModuleNamesTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
                $i=0;
		foreach(range(1, 10) as $index)
		{
			SubModuleName::create([
                                'sub_module_id' =>++$i,
                                'sub_module_name' => $faker->word,
                                'module_id'=> rand(2,3),
                                'sub_module_url' =>  str_replace('.', '/', $faker->unique()->userName),
                                'sub_module_icon' => $faker->word,
                                'sorting' => $i,
                                'year' => 2015

			]);
		}
	}

}