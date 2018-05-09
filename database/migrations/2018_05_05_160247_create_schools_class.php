<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchoolsClass extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schools', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id', 50)->unique()->nullable(false)->comment('学校编号');
            $table->string('name', 100)->nullable(false)->comment('学校名称');
            $table->string('description', 128) ->nullable()->comment('学校简介');
            $table->boolean('is_valid')->nullable(false)->default('1')->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');
            $table->index('name');
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
        Schema::dropIfExists('schools');
    }
}
