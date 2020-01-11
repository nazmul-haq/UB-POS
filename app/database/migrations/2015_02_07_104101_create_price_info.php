<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePriceInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('PriceInfos', function(Blueprint $table)
		{
                        $table->increments('price_id');
                        $table->integer('item_id')->unsigned();
                        $table->double('purchase_price', 12, 3);
                        $table->double('sale_price', 12, 3);

                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('PriceInfos', function(Blueprint $table)
                {
                    $table->index('price_id');
                    $table->primary(array('item_id', 'purchase_price', 'sale_price'));
                    $table->foreign('item_id')->references('item_id')->on('ItemInfos')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('PriceInfos');
	}

}
