<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ModuleEmpPermissionTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
                 $i=3;
		foreach(range(1, 3) as $index)
		{
			ModuleEmpPermission::create([
                                'module_id' =>++$i,
                                'emp_id' => 1,
                                'year' => 2015,
                                'created_by' => 1

			]);
		}
	}

}