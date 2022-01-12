<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->string('user_id', 50)->charset('utf8');
            $table->string('fname', 255)->charset('utf8');
            $table->string('lname', 255)->charset('utf8');
            $table->integer('age')->unsigned()->index();
            $table->enum('sex', ['M', 'F'])->charset('utf8')->default('M');
            $table->enum('type', ['0', '1', '2'])->charset('utf8')->default('0');
            $table->string('phone', 10)->charset('utf8');
            $table->string('line', 255)->charset('utf8');
            $table->string('email')->unique();
            $table->string('password', 100)->charset('utf8')->nullable();
            $table->string('image', 255)->charset('utf8')->nullable();
            $table->string('create_by', 100)->charset('utf8')->nullable();
            $table->string('update_by', 100)->charset('utf8')->nullable();

            $table->timestamps();
            $table->softDeletes();
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
