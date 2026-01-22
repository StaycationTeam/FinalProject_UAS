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
        Schema::table('battles', function (Blueprint $table) {
            // Drop old unused columns if they exist
            if (Schema::hasColumn('battles', 'attacker_power')) {
                $table->dropColumn('attacker_power');
            }
            if (Schema::hasColumn('battles', 'defender_power')) {
                $table->dropColumn('defender_power');
            }
            if (Schema::hasColumn('battles', 'result')) {
                $table->dropColumn('result');
            }
            if (Schema::hasColumn('battles', 'battle_log')) {
                $table->dropColumn('battle_log');
            }
            if (Schema::hasColumn('battles', 'battle_time')) {
                $table->dropColumn('battle_time');
            }
            
            // Add winner_id if it doesn't exist
            if (!Schema::hasColumn('battles', 'winner_id')) {
                $table->foreignId('winner_id')->nullable()->after('gold_stolen')->constrained('kingdoms');
            }
            
            // Add default value to type field if it exists
            if (Schema::hasColumn('battles', 'type')) {
                $table->string('type')->default('pvp')->change();
            }
            
            // Make sure troops columns are nullable with defaults
            $table->integer('attacker_troops')->default(0)->change();
            $table->integer('defender_troops')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('battles', function (Blueprint $table) {
            // Restore old columns
            $table->integer('attacker_power')->nullable();
            $table->integer('defender_power')->nullable();
            $table->string('result')->nullable();
            $table->text('battle_log')->nullable();
            $table->timestamp('battle_time')->nullable();
            
            // Remove winner_id
            if (Schema::hasColumn('battles', 'winner_id')) {
                $table->dropForeign(['winner_id']);
                $table->dropColumn('winner_id');
            }
        });
    }
};
