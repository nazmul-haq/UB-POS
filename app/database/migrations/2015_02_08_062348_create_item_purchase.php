<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemPurchase extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ItemPurchases', function(Blueprint $table)
		{
			$table->increments('i_purchase_id');
                        $table->integer('sup_invoice_id')->unsigned();
                        $table->integer('item_id')->unsigned();
                        $table->integer('price_id')->unsigned();
                        $table->integer('quantity');
                        $table->double('discount', 12, 3);
                        $table->double('amount', 12, 3);

                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('ItemPurchases', function(Blueprint $table)
                {
                    $table->index('i_purchase_id');
                    $table->foreign('sup_invoice_id')->references('sup_invoice_id')->on('SupInvoices')->onDelete('cascade')->onUpdate('CASCADE');
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
		Schema::drop('ItemPurchases');
	}

}
