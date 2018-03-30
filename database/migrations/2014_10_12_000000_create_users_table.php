<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('unique_id', 50)->unique()->nullable(false)->comment('学号、教职工号');
            $table->string('name', 100)->nullable(false)->comment('姓名');
            $table->string('email')->unique()->nullable()->comment('邮箱');
            $table->string('mobile', 35)->unique()->nullable()->comment('手机号');
            $table->string('picture', 35)->unique()->nullable()->comment('头像地址');
            $table->string('password')->nullable(false)->default(Hash::make(config('database.defaultUserPwd')))->comment('密码');
            $table->boolean('is_valid') ->default('1') ->comment('有效标识');
            $table->string('ext', 100) ->nullable() ->comment('备注');
            $table->rememberToken();
            $table->timestamps();
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
        Schema::dropIfExists('users');
    }
}
