<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('country_views', function (Blueprint $table) {
            $table->id();
            $table->string('code', 2);
            $table->unsignedBigInteger('views')->default(0);
            $table->timestamps();

            $table->unique('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('country_views');
    }
};
