<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// A "migration" is just a PHP file that describes a database table.
// Running `php artisan migrate` executes every migration file in order,
// so your whole team (and every environment: local, staging, production)
// ends up with the exact same table structure without anyone typing raw SQL.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id(); // auto-incrementing primary key, same as INTEGER PRIMARY KEY AUTOINCREMENT in SQLite
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('name');
            $table->string('logo_url')->nullable();
            $table->string('primary_color')->default('#0d6efd');
            $table->string('secondary_color')->default('#111827');
            $table->string('google_review_url')->nullable();
            $table->timestamps(); // adds created_at / updated_at columns automatically
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
