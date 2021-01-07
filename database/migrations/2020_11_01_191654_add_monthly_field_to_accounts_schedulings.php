<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonthlyFieldToAccountsSchedulings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_schedulings', function (Blueprint $table) {
            $table->boolean('monthly')->after('paid')->default(false);
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
            $table->dropColumn('monthly');
        });
    }
}
