<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table ->string('isbn', 20) ->nullable(false) ->unique() ->comment('书籍编号');
            $table ->string('name', 256) ->nullable(false) ->comment('书籍名称');
            $table ->string('description', 512) ->nullable(true) ->comment('书籍简介');
            $table ->string('author', 256) ->nullable(false) ->comment('书籍作者');
            $table ->string('publishing', 128) ->nullable(false) ->comment('书籍出版社');
            $table ->decimal('price', 6, 2) ->nullable(false) ->comment('书籍单价');
            $table ->float('discount', 5, 2) ->nullable(false) ->default('100.00') ->comment('书籍折扣');
            $table ->integer('type') ->nullable(false) ->default(1) ->comment('书籍类型:1 教科类；2 教参类');
            $table ->integer('course_id') ->nullable(false) ->comment('courses.id');
            $table ->boolean('is_valid') ->nullable(false) ->default(1) ->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');
            $table ->index('course_id');
            $table ->index('name');
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
        Schema::dropIfExists('books');
    }
}
