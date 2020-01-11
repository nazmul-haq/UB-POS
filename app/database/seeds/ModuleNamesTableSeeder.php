<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;

class ModuleNamesTableSeeder extends Seeder {

	public function run()
	{

            
            $module_name=array();
            $module_url=array();
            $module_icon=array();
            $module_sorting=array();


            $module_name[1]="Permission";
            $module_url[1]="permission";
            $module_icon[1]="permission.png";
            $module_sorting[1]=1;
            $module_status[1]=0; //0 means this menu setup by default

            $module_name[2]="Configuration";
            $module_url[2]="config";
            $module_icon[2]="config.png";
            $module_sorting[2]=2;
            $module_status[2]=0;
            
            $module_name[3]="Dashboard";
            $module_url[3]="index";
            $module_icon[3]="dashboard.png";
            $module_sorting[3]=0;
            $module_status[3]=0;
            
            $module_name[4]="Employees";
            $module_url[4]="employees";
            $module_icon[4]="employees.png";
            $module_sorting[4]=14;
            $module_status[4]=0;

            $module_name[5]="Items";
            $module_url[5]="items";
            $module_icon[5]="items.png";
            $module_sorting[5]=3;
            $module_status[5]=0;

            $module_name[6]="Suppliers";
            $module_url[6]="suppliers";
            $module_icon[6]="suppliers.png";
            $module_sorting[6]=4;

            $module_name[7]="Customers";
            $module_url[7]="customers";
            $module_icon[7]="customers.png";
            $module_sorting[7]=5;

            $module_name[8]="Purchase";
            $module_url[8]="purchase";
            $module_icon[8]="purchase.png";
            $module_sorting[8]=6;

            $module_name[9]="Sales";
            $module_url[9]="sales";
            $module_icon[9]="sales.png";
            $module_sorting[9]=7;
            $module_status[9]=0;

            $module_name[10]="Store House";
            $module_url[10]="StoreHouse/bbb";
            $module_icon[10]="storhug.png";
            $module_sorting[10]=8;

            $module_name[11]="Others";
            $module_url[11]="others";
            $module_icon[11]="others.png";
            $module_sorting[11]=9;

            $module_name[12]="Reports";
            $module_url[12]="reports";
            $module_icon[12]="reports.png";
            $module_sorting[12]=10;
            $module_status[12]=0;

            $module_name[13]="Payroll";
            $module_url[13]="payroll";
            $module_icon[13]="payroll.png";
            $module_sorting[13]=11;

            $module_name[14]="Projects";
            $module_url[14]="projects";
            $module_icon[14]="projects.png";
            $module_sorting[14]=12;
            
            $module_name[15]="Random Setup";
            $module_url[15]="random_setup";
            $module_icon[15]="random_setup.png";
            $module_sorting[15]=13;
            

		$faker = Faker::create();
                $i=0;
		foreach(range(1, 15) as $index)
		{   $i++;
			ModuleName::create([

                                'module_id' => $i,
                                'module_name' => $module_name[$i],
                                'module_url' => $module_url[$i],
                                'icon' => $module_icon[$i],
                                'sorting' => $module_sorting[$i],
                                'year' => 2015,
                                'status'=>(isset($module_status[$i])?$module_status[$i]:1)

			]);
		}
	}

}