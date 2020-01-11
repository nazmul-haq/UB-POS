<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReturnReceivingItem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ReturnReceivingItems', function(Blueprint $table)
		{
                        $table->increments('r_receiving_item_id');
                        $table->integer('item_id')->unsigned();
                        $table->integer('price_id')->unsigned();
                        $table->integer('quantity');
                        $table->dateTime('sending_date');
                        $table->dateTime('receiving_date');


                        $table->integer('status')->default(1)->comment('1=pending, 0=received');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('ReturnReceivingItems', function(Blueprint $table)
                {
                    $table->index('r_receiving_item_id');
                    $table->foreign('item_id')->references('item_id')->on('ItemInfos')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('price_id')->references('price_id')->on('PriceInfos')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('ReturnReceivingItems');
	}

}
