<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            $table->string('provider_type')->default('admin'); // admin | custom
            $table->string('provider_name')->nullable();
            $table->string('provider_avatar')->nullable();
            $table->boolean('use_global_contact')->default(true);
            $table->boolean('allow_inquiry')->default(true);
            // Contact links (used for custom providers, or overrides).
            $table->string('whatsapp')->nullable();
            $table->string('telegram')->nullable();
            $table->string('instagram')->nullable();
            $table->string('twitter')->nullable();
            $table->string('github')->nullable();
            $table->string('discord')->nullable();
            $table->string('facebook')->nullable();
            $table->string('custom_label')->nullable();
            $table->string('custom_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
