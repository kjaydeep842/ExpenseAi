<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('category_id')->nullable();
            $table->uuid('merchant_id')->nullable();
            $table->string('period')->default('monthly'); // daily, weekly, monthly, yearly
            $table->decimal('amount', 15, 2);
            $table->decimal('spent', 15, 2)->default(0.00);
            $table->integer('threshold_percentage')->default(80);
            $table->boolean('is_alert_enabled')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('cascade');
        });

        Schema::create('budget_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('budget_id');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('allocated_amount', 15, 2);
            $table->decimal('spent_amount', 15, 2);
            $table->timestamps();

            $table->foreign('budget_id')->references('id')->on('budgets')->onDelete('cascade');
        });

        Schema::create('goals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('title');
            $table->decimal('target_amount', 15, 2);
            $table->decimal('current_amount', 15, 2)->default(0.00);
            $table->date('deadline')->nullable();
            $table->string('category')->default('savings'); // emergency, vacation, vehicle, house, investment, savings
            $table->string('status')->default('active'); // active, completed, cancelled
            $table->string('icon')->default('sparkles');
            $table->string('color', 20)->default('#10b981');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('goal_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('goal_id');
            $table->uuid('user_id');
            $table->uuid('transaction_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('note')->nullable();
            $table->string('type')->default('deposit'); // deposit, withdraw
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('merchant_id')->nullable();
            $table->uuid('category_id')->nullable();
            $table->string('name');
            $table->decimal('amount', 15, 2);
            $table->string('billing_cycle')->default('monthly'); // monthly, yearly, weekly
            $table->date('next_billing_date');
            $table->boolean('auto_renew')->default(true);
            $table->string('status')->default('active'); // active, cancelled, paused
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('merchant_id')->references('id')->on('merchants')->onDelete('set null');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });

        Schema::create('subscription_histories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('subscription_id');
            $table->uuid('transaction_id')->nullable();
            $table->decimal('paid_amount', 15, 2);
            $table->date('payment_date');
            $table->string('status')->default('success');
            $table->timestamps();

            $table->foreign('subscription_id')->references('id')->on('subscriptions')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });

        Schema::create('recurring_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('category_id')->nullable();
            $table->uuid('merchant_id')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('frequency')->default('monthly'); // daily, weekly, monthly, yearly
            $table->date('next_run_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_transactions');
        Schema::dropIfExists('subscription_histories');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('goal_transactions');
        Schema::dropIfExists('goals');
        Schema::dropIfExists('budget_histories');
        Schema::dropIfExists('budgets');
    }
};
