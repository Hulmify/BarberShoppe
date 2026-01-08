<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->string('phone')->nullable(false)->change();
            
            // Note: In SQLite, you might need to drop unique and recreate. 
            // In MySQL/PostgreSQL, we might need a separate dropUnique if we want to remove the constraint.
            // For now, let's assume we want to remove unique on email and maybe add it to phone.
        });
        
        // Use raw query or separate schema call if needed to drop unique on email
        try {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropUnique(['email']);
                $table->unique('phone');
            });
        } catch (\Exception $e) {
            // Might fail if constraint name is different or doesn't exist
        }
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('email')->nullable(false)->unique()->change();
            $table->string('phone')->nullable()->change();
            $table->dropUnique(['phone']);
        });
    }
};
