<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            //
            $table->increments('id');
            $table->string('unique_id', 50)->unique()->nullable(false)->comment('班级编号');
            $table->string('name', 100)->nullable(false)->comment('班级名称');
            $table->string('description', 128) ->nullable()->comment('班级简介');
            $table->integer('grade') ->nullable(false) ->comment('年级');
            $table->integer('major_id') ->nullable(false) ->comment('专业id，majors.id');
            $table->boolean('is_valid')->nullable(false)->default('1')->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');
            $table->index('name');
            $table->index('major_id');
            $table->engine='InnoDB';
            $table->charset='utf8';
            $table->collation='utf8_unicode_ci';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
