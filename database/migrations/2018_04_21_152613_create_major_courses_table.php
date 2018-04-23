<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMajorCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('major_courses', function (Blueprint $table) {
            $table->increments('id');
            $table ->integer('major_id') ->nullable(false) ->comment('majors.id');
            $table ->integer('course_id') ->nullable(false) ->comment('courses.id');
            $table ->boolean('is_valid') ->nullable(false) ->default(1) ->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');
            $table ->index('major_id');
            $table ->index('course_id');
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
        Schema::dropIfExists('major_courses');
    }
}
