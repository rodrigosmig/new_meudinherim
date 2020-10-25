<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceIdFieldToAccountsSchedulings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('accounts_schedulings', function (Blueprint $table) {
            $table->unsignedBigInteger('invoice_id')->nullable()->after('category_id');
            $table->foreign('invoice_id')->references('id')->on('invoices');
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
            $table->dropForeign('accounts_schedulings_invoice_id_foreign');
            $table->dropColumn('invoice_id');
        });
    }
}
