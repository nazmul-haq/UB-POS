<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemInfo extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ItemInfos', function(Blueprint $table)
		{
			$table->increments('item_id');
                        $table->string('item_name', 30);
                        $table->string('upc_code', 256);
                        $table->integer('category_id')->unsigned();
                        $table->integer('brand_id')->unsigned();
                        $table->integer('price_id')->unsigned();
                        $table->double('tax_amount', 12, 3);
                        $table->integer('offer');
                        $table->text('location');
                        $table->text('description');


                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                 Schema::table('ItemInfos', function(Blueprint $table)
                {
                    $table->index('item_id');
                    $table->foreign('category_id')->references('category_id')->on('ItemCategorys')->onDelete('cascade')->onUpdate('CASCADE');
                    $table->foreign('brand_id')->references('brand_id')->on('ItemBrands')->onDelete('cascade')->onUpdate('CASCADE');
                    
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
		Schema::drop('ItemInfos');
	}

}
