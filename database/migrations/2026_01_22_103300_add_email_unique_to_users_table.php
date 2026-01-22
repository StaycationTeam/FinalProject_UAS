<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if email column already has unique constraint
        $sm = Schema::getConnection()->getDoctrineSchemaManager();
        $indexesFound = $sm->listTableIndexes('users');
        
        Schema::table('users', function (Blueprint $table) use ($indexesFound) {
            // Drop existing index if exists
            foreach ($indexesFound as $index) {
                if ($index->isUnique() && in_array('email', $index->getColumns())) {
                    // Already has unique constraint, skip
                    return;
                }
            }
            
            // Add unique constraint
            $table->unique('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropUnique(['email']);
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
        });
    }
};
