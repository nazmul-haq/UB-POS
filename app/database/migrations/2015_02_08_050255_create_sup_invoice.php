<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupInvoice extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SupInvoices', function(Blueprint $table)
		{
			$table->increments('sup_invoice_id');
                        $table->integer('supp_id')->unsigned();
                        $table->integer('payment_type_id')->unsigned();
                        $table->double('discount', 12, 3);
                        $table->double('amount', 12, 3);
                        $table->double('pay', 12, 3);
                        $table->double('due', 12, 3);
                        $table->date('date');


                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('SupInvoices', function(Blueprint $table)
                {
                    $table->index('sup_invoice_id');
                    $table->foreign('supp_id')->references('supp_id')->on('SupplierInfos')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('SupInvoices');
	}

}
