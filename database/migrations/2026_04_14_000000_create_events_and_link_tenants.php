<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('tenants', function (Blueprint $table) {
            $table->foreignId('event_id')->nullable()->after('id')->constrained()->nullOnDelete();
        });

        $now = now();
        $eventId = DB::table('events')->insertGetId([
            'name' => 'Acara Default',
            'description' => 'Event bawaan untuk tenant yang sudah ada sebelum fitur acara.',
            'starts_at' => null,
            'ends_at' => null,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('tenants')->whereNull('event_id')->update(['event_id' => $eventId]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
        });

        Schema::dropIfExists('events');
    }
};
