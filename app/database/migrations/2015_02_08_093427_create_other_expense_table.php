<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOtherExpenseTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('OtherExpenses', function(Blueprint $table)
		{
                        $table->increments('other_expense_id');
                        $table->text('expense_reasion');
                        $table->double('amount', 12, 3);
                        $table->text('comment');
                        $table->date('date');
                        
                        $table->integer('status')->default(1)->comment('1=pending, 0=received');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('OtherExpenses', function(Blueprint $table)
                {
                    $table->index('other_expense_id');
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
		Schema::drop('OtherExpenses');
	}

}
