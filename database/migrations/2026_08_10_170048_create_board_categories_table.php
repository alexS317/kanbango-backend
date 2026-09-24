<?php

use App\Models\Board;
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
        Schema::create('board_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Board::class)->constrained()->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->integer('position');
            $table->timestamps();
            $table->unique(['board_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_categories');
    }
};
