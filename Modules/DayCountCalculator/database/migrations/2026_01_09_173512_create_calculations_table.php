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
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->string('convention_type');  // '30/360 US', 'Actual/Actual', etc.
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_calculated');
            $table->decimal('day_count_factor', 20, 10);
            $table->decimal('principal', 15, 2)->nullable();
            $table->decimal('interest_rate', 8, 6)->nullable();
            $table->decimal('interest_amount', 15, 2)->nullable();
            $table->json('calculation_steps');  // Store step-by-step breakdown
            $table->string('session_id', 40)->nullable();  // For guest users
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['convention_type', 'created_at']);
            $table->index('session_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
