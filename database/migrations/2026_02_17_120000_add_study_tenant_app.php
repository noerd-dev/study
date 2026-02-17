<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        try {
            DB::statement("
                INSERT INTO `tenant_apps` (`title`, `name`, `icon`, `route`, `is_active`, `created_at`, `updated_at`)
                SELECT 'Study', 'STUDY', 'study::icons.app', 'study.dashboard', 1, NOW(), NOW()
                WHERE NOT EXISTS (
                    SELECT 1 FROM `tenant_apps` WHERE `name` = 'STUDY'
                )
            ");
        } catch (\Exception $e) {
            Log::info('Study tenant app entry might already exist: ' . $e->getMessage());
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            DB::table('tenant_apps')->where('name', 'STUDY')->delete();
        } catch (\Exception $e) {
            Log::info('Could not remove Study tenant app entry: ' . $e->getMessage());
        }
    }
};
