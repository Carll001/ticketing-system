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
        // Proofs table
        Schema::create('proofs', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('step_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignUuid('user_id')
      ->constrained('users')
      ->onDelete('cascade');


            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

           
        });

        // Attachments table
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('proof_id')
                  ->constrained('proofs')
                  ->onDelete('cascade');

            $table->string('original_name');
            $table->string('path');
            $table->string('mime');
            $table->unsignedBigInteger('size');
            $table->timestamps();
            $table->softDeletes();

            $table->index('proof_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attachments');
        Schema::dropIfExists('proofs');
    }
};
