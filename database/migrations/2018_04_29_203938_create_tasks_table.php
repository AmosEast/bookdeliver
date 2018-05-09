<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table ->string('name', 64) ->nullable(false) ->comment('任务名称');
            $table ->string('description', 256) ->nullable(false) ->comment('任务描述或备注');
            $table ->integer('status') ->nullable(false) ->default(0) ->comment('任务状态');
            $table ->boolean('is_valid') ->nullable(false) ->default(1) ->comment('有效标识');
            $table->timestamps();
            $table->bigInteger('creator_id')->nullable(false)->comment('创建者id,users.id');
            $table->bigInteger('updater_id')->nullable(false)->comment('更新者id,users.id');

            $table ->index('name');
            $table ->index(['status', 'is_valid']);

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
        Schema::dropIfExists('tasks');
    }
}
