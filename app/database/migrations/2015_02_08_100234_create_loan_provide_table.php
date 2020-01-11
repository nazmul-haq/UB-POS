<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanProvideTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('LoanProvides', function(Blueprint $table)
		{
                        $table->increments('loan_provide_id');
                        $table->text('company_person_name')->comment('it can be person or company name');
                        $table->double('amount', 12, 3);
                        $table->text('reasion');
                        $table->text('comment');
                        $table->date('date');
                        $table->double('pay_amount', 12, 3)->default(0)->comment('will be update after each paid transaction');

                        $table->integer('status')->default(1)->comment('1=not paid, 0=paid');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('LoanProvides', function(Blueprint $table)
                {
                    $table->index('loan_provide_id');
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
		Schema::drop('LoanProvides');
	}

}
