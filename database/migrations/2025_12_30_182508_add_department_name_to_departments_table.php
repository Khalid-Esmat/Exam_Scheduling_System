<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Adds the column after 'department_code' for better organization
            $table->string('department_name')->nullable()->after('department_code');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Removes the column if the migration is rolled back
            $table->dropColumn('department_name');
        });
    }
};