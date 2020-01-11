<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmpInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('EmpInfos', function(Blueprint $table)
		{
			
                        $table->increments('emp_id');
                        $table->integer('role')->default(1)->comment('0=g_emp, 1=s_emp, 2=super_admin');
                        $table->string('f_name', 20);
                        $table->string('l_name', 20);
                        $table->string('father_name', 30);
                        $table->string('mother_name', 30);
                        $table->string('mobile', 15);
                        $table->string('email', 50);
                        $table->string('user_name', 30);
                        $table->string('password', 256);
                        $table->text('permanent_address');
                        $table->text('present_address');
                        $table->text('profile_image');
                        $table->bigInteger('national_id');
                        $table->integer('fixed_salary');
                        $table->integer('advance_salary');
                        $table->integer('due_salary');

                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->string('remember_token', 255);
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
                        
			$table->timestamps();
		});

                Schema::table('EmpInfos', function(Blueprint $table)
                {
                    $table->index('emp_id');
                    $table->unique('user_name');
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
		Schema::drop('EmpInfos');
	}

}
