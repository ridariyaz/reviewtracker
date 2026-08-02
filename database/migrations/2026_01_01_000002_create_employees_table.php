<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('scans')->default(0);
            $table->unsignedInteger('good_count')->default(0);
            $table->unsignedInteger('ok_count')->default(0);
            $table->unsignedInteger('bad_count')->default(0);
            $table->string('employee_username')->nullable()->unique();
            $table->string('employee_password')->nullable(); // stores a hashed password, same idea as Werkzeug's generate_password_hash
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
