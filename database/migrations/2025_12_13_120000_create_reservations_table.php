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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('reference_id')->index();

            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('item_id')->index();

            $table->integer('quantity')->default(1);
            $table->dateTimeTz('start_at');
            $table->dateTimeTz('end_at')->nullable();

            $table->enum('status', ['pending','held','confirmed','cancelled','completed','expired','failed'])->default('pending')->index();
            $table->dateTimeTz('hold_expires_at')->nullable();

            $table->string('idempotency_key')->nullable();

            $table->decimal('price_amount', 12, 2)->nullable();
            $table->string('price_currency', 8)->default('PHP');
            $table->string('payment_status')->nullable()->index();
            $table->string('payment_provider')->nullable();
            $table->string('payment_reference')->nullable();

            $table->json('meta')->nullable();
            $table->json('customer_data')->nullable();

            $table->string('lock_token')->nullable();
            $table->integer('version')->default(1);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->string('cancel_reason')->nullable();

            $table->string('channel')->nullable()->index();
            $table->text('recurrence_rule')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // indexes and constraints
            $table->index(['tenant_id', 'item_id']);
            $table->index(['tenant_id', 'start_at']);
            $table->index(['tenant_id', 'start_at', 'end_at']);
            $table->unique(['tenant_id', 'idempotency_key']);

            // optional foreign keys (commented): enable if referenced tables exist and you want FK enforcement
            // $table->foreign('tenant_id')->references('id')->on('tenants');
            // $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreignUuid('order_reference')->references('order_reference')->on('orders');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
