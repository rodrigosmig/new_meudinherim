<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parcels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('date');
            $table->date('paid_date')->nullable();
            $table->string('description');
            $table->integer('value');
            $table->unsignedBigInteger('category_id');
            $table->tinyInteger('parcel_number');
            $table->tinyInteger('parcel_total');
            $table->boolean('paid')->default(false);
            $table->morphs('parcelable');
            $table->unsignedBigInteger('invoice_id')->nullable();
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
        Schema::dropIfExists('parcels');
    }
}
