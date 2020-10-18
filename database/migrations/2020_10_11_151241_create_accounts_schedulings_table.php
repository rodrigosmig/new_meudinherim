<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccountsSchedulingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accounts_schedulings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('description');
            $table->integer('value');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('user_id');
            $table->boolean('paid')->default(false);
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('accounts_schedulings');
    }
}
