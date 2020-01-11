<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('CustomerInfos', function(Blueprint $table)
		{
			$table->increments('cus_id');
                        $table->integer('cus_type_id')->unsigned();
                        
                        $table->string('full_name', 50);
                        $table->string('user_name', 30);
                        $table->string('password', 256);
                        $table->text('permanent_address');
                        $table->text('present_address');
                        $table->text('profile_image');
                        $table->bigInteger('national_id');
                        $table->string('mobile', 15);
                        $table->string('email', 50);
                        $table->string('cus_card_id', 50);
                        $table->integer('advance_payment');
                        $table->integer('due');
                        $table->integer('point');

                        

                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
                        
		});
                Schema::table('CustomerInfos', function(Blueprint $table)
                {
                    $table->index('cus_id');
                    $table->unique('user_name');
                    $table->unique('cus_card_id');
                  
                    $table->foreign('cus_type_id')->references('cus_type_id')->on('CustomerTypes')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('CustomerInfos');
	}

}
