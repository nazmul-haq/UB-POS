<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		// $this->call('UserTableSeeder');
                //$this->call('EmpInfosTableSeeder');
                $this->call('ModuleNamesTableSeeder');
                //$this->call('ModulePermissionsTableSeeder');
                //$this->call('SubModuleNamesTableSeeder');
                //$this->call('ModuleEmpPermissionTableSeeder');
				//$this->call('SMEmpPermissionsTableSeeder');
	}

}
