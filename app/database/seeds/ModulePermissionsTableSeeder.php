<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ModulePermissionsTableSeeder extends Seeder {

	public function run()
	{
		$faker = Faker::create();
                $i=0;
		foreach(range(1, 8) as $index)
		{    
			ModulePermission::create([
                              
                                'module_id'=> ++$i,
                                'company_id'=> 1,
                                'year' => 2015
			]);
		}
	}

}