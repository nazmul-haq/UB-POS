<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubModuleNameTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('SubModuleNames', function(Blueprint $table)
		{
                        $table->increments('sub_module_id');
                        $table->string('sub_module_name', 30);
                        $table->integer('module_id')->unsigned();
                        $table->text('sub_module_url');
                        $table->text('sub_module_icon');
                        $table->integer('sorting')->comment('For showing sub module with user define sequence');

                        $table->integer('status')->default(1)->comment('0=Inactive, 1=Active');
                        $table->integer('year');
			$table->timestamps();
		});
                Schema::table('SubModuleNames', function(Blueprint $table)
                {
                    $table->index('sub_module_id');
                    $table->foreign('module_id')->references('module_id')->on('ModulePermissions')->onDelete('cascade')->onUpdate('CASCADE');
                });


	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('SubModuleNames');
	}

}
