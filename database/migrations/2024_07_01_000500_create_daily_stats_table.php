<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('stat_date')->unique();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('logins')->default(0);
            $table->unsignedInteger('password_resets')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_stats');
    }
};
