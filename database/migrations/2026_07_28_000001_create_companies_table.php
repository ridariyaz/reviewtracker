<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('primary_color')->default('#0d6efd');
            $table->string('secondary_color')->default('#111827');
            $table->string('google_review_url')->nullable();
            $table->string('industry')->nullable();
            $table->text('keywords')->nullable();
            $table->string('default_platform')->default('google');
            $table->boolean('enable_gamification')->default(false);
            $table->integer('gamification_interval')->default(50);
            $table->string('gamification_reward')->default('Free Coffee / Gift Voucher');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
