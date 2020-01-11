<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupplierInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SupplierInfos', function(Blueprint $table)
		{
			$table->increments('supp_id');
                        $table->string('supp_or_comp_name', 100);
                        $table->string('user_name', 30);
                        $table->string('password', 256);
                        $table->text('permanent_address');
                        $table->text('present_address');
                        $table->text('profile_image');
                        $table->string('mobile', 15);
                        $table->string('email', 50);
                        $table->integer('advance_payment')->comment('Shop advance payment');
                        $table->integer('due')->comment('Shop liabilities');
                        
                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();

		});
                Schema::table('SupplierInfos', function(Blueprint $table)
                {
                    $table->index('supp_id');
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
		Schema::drop('SupplierInfos');
	}

}
