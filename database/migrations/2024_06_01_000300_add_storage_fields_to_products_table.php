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
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'file_type')) {
                // 'upload' = file stored on the configured disk, 'external' = hosted elsewhere via link.
                $table->string('file_type', 20)->default('upload')->after('file_size');
            }
            if (! Schema::hasColumn('products', 'external_url')) {
                $table->text('external_url')->nullable()->after('file_type');
            }
            if (! Schema::hasColumn('products', 'download_limit')) {
                // Per-buyer download count limit. 0 / null = unlimited.
                $table->unsignedInteger('download_limit')->nullable()->after('external_url');
            }
            if (! Schema::hasColumn('products', 'link_expiry_minutes')) {
                // Validity window (minutes) for a generated download link. 0 / null = never expires.
                $table->unsignedInteger('link_expiry_minutes')->nullable()->after('download_limit');
            }
            if (! Schema::hasColumn('products', 'download_message')) {
                // Optional custom message shown to buyers on the download screen.
                $table->text('download_message')->nullable()->after('link_expiry_minutes');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'file_type',
                'external_url',
                'download_limit',
                'link_expiry_minutes',
                'download_message',
            ]);
        });
    }
};
