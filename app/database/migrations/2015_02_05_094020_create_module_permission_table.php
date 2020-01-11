<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModulePermissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ModulePermissions', function(Blueprint $table)
		{
			$table->integer('m_permission_id' , true);
                        $table->integer('module_id')->unsigned();
                        $table->integer('company_id')->unsigned();
                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
			$table->timestamps();

                        

		});
                
                Schema::table('ModulePermissions', function(Blueprint $table)
                {
                    $table->index('m_permission_id');
                    $table->primary(array('module_id', 'company_id'));
                    $table->foreign('module_id')->references('module_id')->on('ModuleNames')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('company_id')->references('company_id')->on('CompanyProfiles')->onDelete('cascade')->onUpdate('CASCADE');
                });


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ModulePermissions');
	}

}
