<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiseaseDrugsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('disease_drugs', function (Blueprint $table) {
            $table->id();
            $table->integer('disease_id')->nullable()->unsigned()->index();
            $table->foreign('disease_id')->references('id')->on('diseases')->onDelete('cascade');

            $table->integer('drug_id')->nullable()->unsigned()->index();
            $table->foreign('drug_id')->references('id')->on('drugs')->onDelete('cascade');

            $table->string('create_by', 100)->charset('utf8')->nullable();
            $table->string('update_by', 100)->charset('utf8')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('disease_drugs');
    }
}
