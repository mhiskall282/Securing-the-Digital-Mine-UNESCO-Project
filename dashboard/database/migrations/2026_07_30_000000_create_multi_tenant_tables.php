<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create Organizations Table
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // 2. Modify Users Table for Multi-Tenancy and Superadmin Role
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->boolean('is_admin')->default(false); // True for superadmin who manages users
        });

        // 3. Create Devices Table
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->string('api_token', 64)->unique();
            $table->timestamps();
        });

        // 4. Create / Update Live Network Flows Table with Relations
        Schema::create('live_network_flows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained('organizations')->onDelete('cascade');
            $table->foreignId('device_id')->nullable()->constrained('devices')->onDelete('cascade');
            $table->timestamp('timestamp')->useCurrent();
            $table->string('protocol_type');
            $table->string('service');
            $table->string('flag');
            $table->integer('src_bytes');
            $table->integer('hot');
            $table->integer('su_attempted');
            $table->double('serror_rate');
            $table->double('same_srv_rate');
            $table->double('diff_srv_rate');
            $table->double('dst_host_diff_srv_rate');
            $table->string('prediction');
            $table->double('confidence');
            $table->double('latency_ms');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_network_flows');
        Schema::dropIfExists('devices');
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['organization_id']);
            $table->dropColumn(['organization_id', 'is_admin']);
        });
        Schema::dropIfExists('organizations');
    }
};
