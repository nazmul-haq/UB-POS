<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCusDuePayment extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('CusDuePayments', function(Blueprint $table)
		{
			$table->increments('c_due_payment_id');
                        $table->integer('cus_id')->unsigned();
                        $table->integer('payment_type_id')->unsigned();
                        $table->double('amount', 12, 3);
                        $table->date('date');

                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('CusDuePayments', function(Blueprint $table)
                {
                    $table->index('c_due_payment_id');
                    $table->foreign('cus_id')->references('cus_id')->on('CustomerInfos')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('payment_type_id')->references('payment_type_id')->on('PaymentTypes')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('CusDuePayments');
	}

}
