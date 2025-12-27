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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->string('domain')->unique();
            $table->string('owner_email')->unique()->nullable()->index();
            $table->string('owner_name')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('address')->nullable();
            $table->string('business_email')->index();
            $table->text('business_description')->nullable();
            
            $table->string('status')->default('active')->index();
            $table->enum('plan', ['free', 'basic', 'premium'])->default('free')->index();
            $table->json('settings')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
