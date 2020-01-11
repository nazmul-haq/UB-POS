<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherIncome extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('OtherIncomes', function(Blueprint $table)
		{
                        $table->increments('other_income_id');
                        $table->text('income_reasion');
                        $table->double('amount', 12, 3);
                        $table->text('comment');
                        $table->date('date');


                        $table->integer('status')->default(1)->comment('1=pending, 0=received');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('OtherIncomes', function(Blueprint $table)
                {
                    $table->index('other_income_id');
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
		Schema::drop('OtherIncomes');
	}

}
