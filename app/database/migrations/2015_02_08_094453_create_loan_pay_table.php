<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLoanPayTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('LoanPays', function(Blueprint $table)
		{
                        $table->increments('loan_pay_id');
                        $table->text('company_person_name')->comment('it can be person or company name');
                        $table->double('amount', 12, 3);
                        $table->text('comment');
                        $table->date('date');
                        
                        $table->integer('status')->default(1)->comment('1=Active, 0=Inactive');
                        $table->integer('year');
                        $table->integer('created_by')->unsigned();
                        $table->integer('updated_by')->nullable()->unsigned();
			$table->timestamps();
		});
                Schema::table('LoanPays', function(Blueprint $table)
                {
                    $table->index('loan_pay_id');
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
		Schema::drop('LoanPays');
	}

}
