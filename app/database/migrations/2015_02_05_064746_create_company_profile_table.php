<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyProfileTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('CompanyProfiles', function(Blueprint $table)
		{
			$table->increments('company_id');
                        $table->text('company_name');
                        $table->text('address');
                        $table->string('mobile', 20);
                        $table->text('web_address');
                        $table->integer('return_policy')->default(0)->comment('0=No, 1=Yes');
                        $table->string('language', 30);
                        $table->string('time_zone', 50);
                        $table->integer('print_recipt_a_sale')->default(0)->comment('0=No, 1=Yes');
                        $table->integer('theme')->default(0)->comment('0=style0, 1=style1, 2=style2, 3=style3');
                        $table->integer('install_complete')->default(0)->comment('0=No, 1=Yes');
                        $table->integer('year');

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('CompanyProfiles');
	}

}
