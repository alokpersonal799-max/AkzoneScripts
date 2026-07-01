<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('theme')->default('custom'); // custom, offer, coming_soon, new_product, maintenance, warning
            $table->string('audience')->default('all');  // all | selected
            $table->string('status')->default('draft');  // draft | scheduled | sent
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->boolean('allow_reply')->default(false);
            $table->json('reply_types')->nullable();      // ['star','emoji','message','media']
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action_url')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcements');
    }
};
