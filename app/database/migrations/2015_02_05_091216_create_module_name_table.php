<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModuleNameTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ModuleNames', function(Blueprint $table)
		{
			$table->increments('module_id');
                        $table->string('module_name', 30);
                        $table->text('module_url');
                        $table->text('icon');
                        $table->integer('sorting')->comment('For showing module with user define sequence');
                        
                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
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
		Schema::drop('ModuleNames');
	}

}
