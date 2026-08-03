<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_superadmin')->default(false)->after('is_admin');
            $table->string('status')->default('active')->after('is_superadmin');
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->text('custom_code')->nullable();
        });

        Schema::create('saas_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_settings');

        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['custom_code']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_superadmin', 'status']);
        });
    }
};
