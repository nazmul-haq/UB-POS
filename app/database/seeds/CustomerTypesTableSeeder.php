<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class CustomerTypesTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();

		foreach(range(1, 3) as $index)
		{
			Customer_type::create([
                                
                                'cus_type_name' => $faker->word,
                                'discount_percent' => rand(2,3),
                                'year' => 2015,
                                'created_by'=> rand(1,2)

			]);
		}
	}

}