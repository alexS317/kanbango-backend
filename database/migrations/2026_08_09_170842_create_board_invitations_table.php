<?php

use App\Models\Board;
use App\Models\User;
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
        Schema::create('board_invitations', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->enum('role', ['admin', 'editor', 'viewer']);
            $table->string('token', 64)->unique();
            $table->foreignIdFor(Board::class, 'board_id')->constrained()->cascadeOnDelete();
            $table->foreignIdFor(User::class, 'invited_by')->constrained();
            $table->timestamp('expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('board_invitations');
    }
};
