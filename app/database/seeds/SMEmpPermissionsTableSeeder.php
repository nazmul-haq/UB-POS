<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class SMEmpPermissionsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
                $i=0;
		foreach(range(1, 10) as $index)
		{
			Smemppermission::create([
                                'sub_module_id' =>++$i,
                                'emp_id' => 1,
                                'year' => 2015,
                                'created_by' => 1

			]);
		}
	}

}