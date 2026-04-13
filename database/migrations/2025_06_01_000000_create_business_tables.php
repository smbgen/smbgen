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
        // Audit Checklists
        if (! Schema::hasTable('audit_checklists')) {
            Schema::create('audit_checklists', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->json('checklist_items');
                $table->boolean('completed')->default(false);
                $table->timestamps();
            });
        }

        // Knowledgebase Articles
        if (! Schema::hasTable('knowledgebase_articles')) {
            Schema::create('knowledgebase_articles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('content');
                $table->string('category')->nullable();
                $table->boolean('published')->default(false);
                $table->timestamps();
            });
        }

        // Purchases
        if (! Schema::hasTable('purchases')) {
            Schema::create('purchases', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('item_name');
                $table->decimal('amount', 10, 2);
                $table->string('vendor')->nullable();
                $table->date('purchase_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Social Accounts
        if (! Schema::hasTable('social_accounts')) {
            Schema::create('social_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('platform'); // Facebook, Instagram, etc.
                $table->string('account_name');
                $table->string('account_url')->nullable();
                $table->text('credentials')->nullable(); // Encrypted
                $table->boolean('active')->default(true);
                $table->timestamps();
            });
        }

        // Clients
        if (! Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->text('notes')->nullable();
                $table->text('message')->nullable();
                $table->string('source_site')->nullable();
                $table->string('notification_email')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Lead Forms
        if (! Schema::hasTable('lead_forms')) {
            Schema::create('lead_forms', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->text('message')->nullable();
                $table->string('source_site')->nullable();
                $table->string('notification_email')->nullable();
                $table->ipAddress('ip_address')->nullable();
                $table->text('user_agent')->nullable();
                $table->string('referer')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_forms');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('social_accounts');
        Schema::dropIfExists('purchases');
        Schema::dropIfExists('knowledgebase_articles');
        Schema::dropIfExists('audit_checklists');
    }
};
