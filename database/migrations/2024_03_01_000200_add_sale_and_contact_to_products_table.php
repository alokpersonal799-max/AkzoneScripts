<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Whether the product can be bought directly on the platform.
            $table->boolean('is_purchasable')->default(true)->after('is_featured');
            // Per-product contact channels (used when not falling back to global).
            $table->boolean('use_global_contact')->default(true)->after('is_purchasable');
            $table->string('contact_whatsapp')->nullable()->after('use_global_contact');
            $table->string('contact_telegram')->nullable()->after('contact_whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['is_purchasable', 'use_global_contact', 'contact_whatsapp', 'contact_telegram']);
        });
    }
};
