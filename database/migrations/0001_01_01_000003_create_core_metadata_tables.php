<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
            DB::statement('CREATE EXTENSION IF NOT EXISTS unaccent');
        }

        Schema::create('salesforce_orgs', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('login_url')->nullable();
            $table->string('instance_url')->nullable();
            $table->string('api_version')->default('61.0');
            $table->string('status')->default('pending');
            $table->timestamp('connected_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sync_runs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('salesforce_org_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->default('pending')->index();
            $table->string('triggered_by')->default('manual');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->json('counts')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('metadata_snapshots', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('salesforce_org_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sync_run_id')->nullable()->constrained()->nullOnDelete();
            $table->string('snapshot_type')->index();
            $table->string('path');
            $table->string('content_hash', 64)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('metadata_entities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('salesforce_org_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_key');
            $table->string('metadata_type')->index();
            $table->string('api_name')->nullable()->index();
            $table->string('label')->nullable();
            $table->string('parent_external_key')->nullable()->index();
            $table->string('status')->default('active')->index();
            $table->string('content_hash', 64)->nullable()->index();
            $table->json('attributes')->nullable();
            $table->timestamp('first_seen_at')->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();

            $table->unique(['salesforce_org_id', 'external_key']);
        });

        Schema::create('metadata_entity_versions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('metadata_entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sync_run_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedInteger('version_number');
            $table->string('content_hash', 64)->index();
            $table->string('payload_path')->nullable();
            $table->json('normalized_payload')->nullable();
            $table->timestamps();

            $table->unique(['metadata_entity_id', 'version_number']);
        });

        Schema::create('metadata_edges', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('salesforce_org_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('source_metadata_entity_id')->nullable()->constrained('metadata_entities')->nullOnDelete();
            $table->foreignId('target_metadata_entity_id')->nullable()->constrained('metadata_entities')->nullOnDelete();
            $table->string('target_external_key')->nullable()->index();
            $table->string('edge_type')->index();
            $table->string('confidence')->default('inferred')->index();
            $table->text('source_detail')->nullable();
            $table->timestamps();
        });

        Schema::create('search_documents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('metadata_entity_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('api_name')->nullable();
            $table->string('metadata_type')->index();
            $table->text('content');
            $table->json('filters')->nullable();
            $table->timestamps();
        });

        Schema::create('dictionary_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('metadata_entity_id')->constrained()->cascadeOnDelete();
            $table->text('business_definition')->nullable();
            $table->string('owner_name')->nullable();
            $table->string('owner_email')->nullable();
            $table->string('data_classification')->default('unknown')->index();
            $table->string('criticality')->default('unknown')->index();
            $table->string('lifecycle_status')->default('active')->index();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tags', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->string('color')->nullable();
            $table->timestamps();
        });

        Schema::create('taggings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->foreignId('metadata_entity_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['tag_id', 'metadata_entity_id']);
        });

        Schema::create('change_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('salesforce_org_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('metadata_entity_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('sync_run_id')->nullable()->constrained()->nullOnDelete();
            $table->string('change_type')->index();
            $table->string('before_hash', 64)->nullable();
            $table->string('after_hash', 64)->nullable();
            $table->json('diff')->nullable();
            $table->timestamps();
        });

        Schema::create('potential_issues', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('salesforce_org_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('metadata_entity_id')->nullable()->constrained()->nullOnDelete();
            $table->string('issue_type')->index();
            $table->string('severity')->default('low')->index();
            $table->string('status')->default('open')->index();
            $table->string('title');
            $table->json('details')->nullable();
            $table->timestamp('detected_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('ignored_at')->nullable();
            $table->foreignId('ignored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('app_audit_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->nullableMorphs('auditable');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX search_documents_content_fts_idx ON search_documents USING gin (to_tsvector('simple', coalesce(title, '') || ' ' || coalesce(api_name, '') || ' ' || coalesce(content, '')))");
            DB::statement('CREATE INDEX search_documents_api_name_trgm_idx ON search_documents USING gin (api_name gin_trgm_ops)');
            DB::statement('CREATE INDEX metadata_entities_api_name_trgm_idx ON metadata_entities USING gin (api_name gin_trgm_ops)');
            DB::statement('CREATE INDEX metadata_entities_label_trgm_idx ON metadata_entities USING gin (label gin_trgm_ops)');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('app_audit_logs');
        Schema::dropIfExists('potential_issues');
        Schema::dropIfExists('change_events');
        Schema::dropIfExists('taggings');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('dictionary_entries');
        Schema::dropIfExists('search_documents');
        Schema::dropIfExists('metadata_edges');
        Schema::dropIfExists('metadata_entity_versions');
        Schema::dropIfExists('metadata_entities');
        Schema::dropIfExists('metadata_snapshots');
        Schema::dropIfExists('sync_runs');
        Schema::dropIfExists('salesforce_orgs');
    }
};
