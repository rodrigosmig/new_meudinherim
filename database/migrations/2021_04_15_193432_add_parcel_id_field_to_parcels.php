<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddParcelIdFieldToParcels extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('account_entries', function (Blueprint $table) {
            $table->unsignedBigInteger('parcel_id')->nullable()->after('account_scheduling_id');
            $table->foreign('parcel_id')->references('id')->on('parcels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('account_entries', function (Blueprint $table) {
            $table->dropForeign('account_entries_parcel_id_foreign');
            $table->dropColumn('parcel_id');
        });
    }
}
