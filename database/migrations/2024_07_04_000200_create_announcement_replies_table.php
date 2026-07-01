<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_admin')->default(false);
            $table->string('type')->default('message'); // star | emoji | message | media
            $table->unsignedTinyInteger('rating')->nullable();
            $table->string('emoji', 16)->nullable();
            $table->text('message')->nullable();
            $table->string('media_path')->nullable();
            $table->timestamps();

            $table->index(['announcement_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_replies');
    }
};
