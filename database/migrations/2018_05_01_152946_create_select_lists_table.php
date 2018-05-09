<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSelectListsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('select_lists', function (Blueprint $table) {
            $table->increments('id');
            $table ->integer('task_id') ->unsigned() ->nullable(false) ->comment('task.id');
            $table ->integer('academy_id') ->unsigned() ->nullable(false) ->comment('academies.id');
            $table ->integer('major_id') ->unsigned() ->nullable(false) ->comment('majors.id');
            $table ->integer('grade') ->unsigned() ->nullable(false) ->comment('年级，例如2014');
            $table ->integer('course_id') ->unsigned() ->nullable(false) ->comment('courses.id');
            $table ->integer('selector_id') ->unsigned() ->nullable(false) ->comment('users.id');
            $table ->string('book_ids', 512) ->nullable(true) ->comment('books.id的json串');
            $table ->integer('status') ->nullable(false) ->default(10) ->comment('审核状态');
            $table ->boolean('is_valid') ->nullable(false) ->default(1) ->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');

            $table ->index('task_id');
            $table ->index(['academy_id', 'major_id']);
            $table ->index('grade');
            $table ->index('course_id');
            $table ->index('selector_id');

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
        Schema::dropIfExists('select_lists');
    }
}
