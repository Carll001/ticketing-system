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
        Schema::create('preset_fields', function (Blueprint $table) {
            $table->uuid('id')->primary();
           
            $table->foreignUuid('preset_id')->constrained()->onDelete('cascade');
            $table->string('type'); // Checkbox, Input, etc.
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preset_fields');
    }
};
