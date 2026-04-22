<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voting_session_id')->constrained('voting_sessions')->cascadeOnDelete();
            $table->string('member_id');
            $table->string('option', 8);
            $table->timestamps();

            $table->unique(['voting_session_id', 'member_id']);
            $table->index(['voting_session_id', 'option']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('votes');
    }
};
