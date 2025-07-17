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
        Schema::create('movie_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movie_list_id')->constrained()->onDelete('cascade');
            $table->integer('tmdb_movie_id'); // ID do filme na API do TMDB
            $table->string('movie_title');
            $table->string('movie_poster_path')->nullable();
            $table->text('movie_overview')->nullable();
            $table->date('movie_release_date')->nullable();
            $table->decimal('movie_vote_average', 3, 1)->nullable();
            $table->text('user_notes')->nullable(); // Anotações pessoais do usuário
            $table->timestamps();
            
            // Previne duplicatas na mesma lista
            $table->unique(['movie_list_id', 'tmdb_movie_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movie_list_items');
    }
};
