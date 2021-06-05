<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnticipatedFieldToParcels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->boolean('anticipated')->default(false)->after('paid');
        });

        Schema::table('invoice_entries', function (Blueprint $table) {
            $table->boolean('anticipated')->default(false)->after('has_parcels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn('anticipated');
        });

        Schema::table('invoice_entries', function (Blueprint $table) {
            $table->dropColumn('anticipated');
        });
    }
}
