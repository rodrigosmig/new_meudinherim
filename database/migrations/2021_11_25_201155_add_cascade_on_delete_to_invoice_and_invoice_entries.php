<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCascadeOnDeleteToInvoiceAndInvoiceEntries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (env('DB_CONNECTION') !== 'sqlite') {
                $table->dropForeign('invoices_card_id_foreign');
                
                $table->foreign('card_id')
                    ->references('id')->on('cards')
                    ->onDelete('cascade');
            }

        });

        Schema::table('invoice_entries', function (Blueprint $table) {
            if (env('DB_CONNECTION') !== 'sqlite') {
                $table->dropForeign('invoice_entries_invoice_id_foreign');

                $table->foreign('invoice_id')
                    ->references('id')->on('invoices')
                    ->onDelete('cascade');
            }

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_entries', function (Blueprint $table) {
            //
        });
    }
}
