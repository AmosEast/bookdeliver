<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
            $table ->integer('belong_id') ->nullable(false) ->comment('根据belong_type来判断是classes.id或者academies.id');
            $table ->integer('belong_type') ->nullable(false) ->default('1') ->comment('1：标识classes，2：标识academies');
            $table ->index(['belong_id', 'belong_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
