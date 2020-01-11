<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class EmpInfosTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
                $i=0;
		foreach(range(1, 10) as $index)
		{       $i++;
			EmpInfo::create([
                                
                                'user_name' => str_replace('.', '_', $faker->unique()->userName),
                                'role'=> rand(0,1),
                                'f_name' => $faker->word,
                                'l_name' => $faker->word,
                                'father_name' => $faker->name,
                                'mother_name' => $faker->name,
                                'mobile' => $faker->unique($reset = true)->randomDigitNotNull,
                                'email' => $faker->email,
                                'password' => $i,
                                'national_id' =>$faker->numberBetween(9,99999999999999999),
                                'permanent_address' => $faker->address,
                                'present_address' => $faker->address,
                                'fixed_salary' => rand(20000,30000),
                                'year' => 2015,
                                'created_by'=> rand(1,2)

			]);
		}
	}

}