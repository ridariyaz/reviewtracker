<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // Drop global unique constraint on employee_username
            try {
                $table->dropUnique(['employee_username']);
            } catch (\Throwable) {
                // SQLite index drop fallback if needed
            }

            // Create composite unique index scoped per company
            $table->unique(['company_id', 'employee_username'], 'employees_company_id_employee_username_unique');
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_company_id_employee_username_unique');
            $table->unique('employee_username');
        });
    }
};
