<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointUsingRecord extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('PointUsingRecords', function(Blueprint $table)
		{
                        $table->increments('point_using_id');
                        $table->integer('cus_id')->unsigned();
                        $table->integer('use_point');
                        $table->integer('benifited_way')->default(1)->comment('1=on item, 0=hand cash');
                        $table->integer('sale_invoice_id')->unsigned();
                        $table->date('date');

                        $table->integer('status')->default(1)->comment('1=Active, 0=Inactive');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();

		});
                Schema::table('PointUsingRecords', function(Blueprint $table)
                {
                    $table->index('point_using_id');
                    $table->foreign('cus_id')->references('cus_id')->on('CustomerInfos')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('sale_invoice_id')->references('sale_invoice_id')->on('SaleInvoices')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('PointUsingRecords');
	}

}
