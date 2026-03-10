<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('sla_policy_id')->nullable()->constrained('sla_policies')->nullOnDelete();
            $table->timestamp('first_response_at')->nullable();
            $table->timestamp('first_response_due_at')->nullable();
            $table->timestamp('resolution_due_at')->nullable();
            $table->boolean('first_response_breached')->default(false);
            $table->boolean('resolution_breached')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('sla_policy_id');
            $table->dropColumn([
                'first_response_at',
                'first_response_due_at',
                'resolution_due_at',
                'first_response_breached',
                'resolution_breached',
            ]);
        });
    }
};
