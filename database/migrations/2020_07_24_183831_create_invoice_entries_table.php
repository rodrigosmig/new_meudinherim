<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoiceEntriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_entries', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->string('description');
            $table->integer('value');
            $table->boolean('monthly')->default(false);
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('invoice_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories');
            $table->foreign('invoice_id')->references('id')->on('invoices');
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
        Schema::dropIfExists('invoice_entries');
    }
}
