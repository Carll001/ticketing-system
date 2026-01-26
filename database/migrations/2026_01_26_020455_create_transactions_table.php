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
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('content');
            $table->string('transaction_number')->unique();
            $table->foreignUuid('user_id')->nullable()->constrained();
            $table->foreignUuid('task_id')->nullable()->constrained();
            $table->foreignUuid('step_id')->nullable()->constrained();
            $table->foreignUuid('department_id')->nullable()->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
