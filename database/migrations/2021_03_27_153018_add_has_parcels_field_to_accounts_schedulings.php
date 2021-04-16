<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHasParcelsFieldToAccountsSchedulings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_schedulings', function (Blueprint $table) {
            $table->boolean('has_parcels')->default(false)->after('monthly');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts_schedulings', function (Blueprint $table) {
            $table->dropColumn('has_parcels');
        });
    }
}
