<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salesforce_orgs', function (Blueprint $table): void {
            $table->string('alias')->nullable()->unique()->after('name');
        });
    }

    public function down(): void
    {
        Schema::table('salesforce_orgs', function (Blueprint $table): void {
            $table->dropUnique(['alias']);
            $table->dropColumn('alias');
        });
    }
};
