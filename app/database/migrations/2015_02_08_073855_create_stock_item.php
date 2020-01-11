<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockItem extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('StockItems', function(Blueprint $table)
		{
                        $table->increments('stock_item_id');
                        $table->integer('item_id')->unsigned();
                        $table->integer('price_id')->unsigned();
                        $table->integer('available_quantity');
                        $table->integer('quantity_ability_flag')->default(1)->comment('0=quantity not available,  1=quentity available');


                        $table->integer('status')->default(1)->comment('0=pending, 1=received');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('StockItems', function(Blueprint $table)
                {
                    $table->index('stock_item_id');
                    $table->primary(array('item_id', 'price_id',));
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
		Schema::drop('StockItems');
	}

}
