<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->string('quickbooks_invoice_id')->nullable()->after('user_id');
            $table->string('quickbooks_customer_id')->nullable()->after('quickbooks_invoice_id');
            $table->text('quickbooks_invoice_url')->nullable()->after('quickbooks_customer_id');
            $table->timestamp('quickbooks_synced_at')->nullable()->after('quickbooks_invoice_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn([
                'quickbooks_invoice_id',
                'quickbooks_customer_id',
                'quickbooks_invoice_url',
                'quickbooks_synced_at',
            ]);
        });
    }
};
