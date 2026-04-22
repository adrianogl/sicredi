<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('motion_id')->constrained('motions')->cascadeOnDelete();
            $table->timestamp('opened_at');
            $table->timestamp('closes_at');
            $table->timestamps();

            $table->index(['motion_id', 'closes_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_sessions');
    }
};
