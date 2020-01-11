<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleEmpPermissionTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('moduleemppermissions', function(Blueprint $table)
		{
			$table->integer('module_emp_p_id' , true);
                        $table->integer('module_id')->unsigned();
                        $table->integer('emp_id')->unsigned();
                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});

                Schema::table('moduleemppermissions', function(Blueprint $table)
                {
                    $table->index('module_emp_p_id');
                    $table->primary(array('module_id', 'emp_id'));
                    $table->foreign('module_id')->references('module_id')->on('ModulePermissions')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('emp_id')->references('emp_id')->on('EmpInfos')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('created_by')->references('emp_id')->on('EmpInfos')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('updated_by')->references('emp_id')->on('EmpInfos')->onDelete('cascade')->onUpdate('CASCADE');
                });
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('moduleemppermissions');
	}

}
