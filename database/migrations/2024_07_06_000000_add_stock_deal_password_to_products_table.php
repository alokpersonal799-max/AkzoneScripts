<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Optional password revealed to buyers on their purchases page.
            $table->string('file_password')->nullable()->after('external_url');
            // Remaining units. NULL = unlimited stock; 0 = out of stock.
            $table->integer('stock')->nullable()->after('sales');
            // Limited-time deal end. NULL = no deal; past = expired.
            $table->timestamp('deal_ends_at')->nullable()->after('stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['file_password', 'stock', 'deal_ends_at']);
        });
    }
};
