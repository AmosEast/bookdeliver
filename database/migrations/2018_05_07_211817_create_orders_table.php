<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table ->integer('task_id') ->unsigned() ->nullable(false) ->comment('tasks.id');
            $table ->integer('select_id') ->unsigned() ->nullable(false) ->comment('select_lists.id');
            $table ->integer('user_id') ->unsigned() ->nullable(false) ->comment('users.id');
            $table ->integer('book_id') ->unsigned() ->nullable(false) ->comment('books.id');
            $table ->integer('quantity') ->unsigned() ->nullable(false) ->comment('订购数量');
            $table ->integer('deliver_sign') ->nullable(false) ->default(0) ->comment('发放标识');
            $table ->integer('receiver_id') ->unsigned() ->nullable(true) ->comment('接收者id，users.id');
            $table ->string('received_ext', 256) ->nullable(true) ->comment('接收书时备注');
            $table->boolean('is_valid')->nullable(false)->default('1')->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');
            $table->index('task_id');
            $table->index('select_id');
            $table->index('user_id');
            $table->index('book_id');
            $table->index('receiver_id');
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
        Schema::dropIfExists('orders');
    }
}
