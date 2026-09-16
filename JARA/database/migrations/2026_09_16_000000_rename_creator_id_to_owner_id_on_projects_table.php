<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('projects', 'creator_id')) {
            Schema::table('projects', function (Blueprint $table): void {
                $table->renameColumn('creator_id', 'owner_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('projects', 'owner_id')) {
            Schema::table('projects', function (Blueprint $table): void {
                $table->renameColumn('owner_id', 'creator_id');
            });
        }
    }
};
