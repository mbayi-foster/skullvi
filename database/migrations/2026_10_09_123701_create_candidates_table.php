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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poste_id')->constrained('postes')->restrictOnDelete();

            // Identité
            $table->string('nom');
            $table->string('email');
            $table->string('telephone', 30)->nullable();

            // Profil
            $table->decimal('annees_experience', 4, 1)->default(0);
            $table->enum('niveau_etudes', ['aucun', 'bac', 'licence', 'master', 'doctorat'])
                ->default('aucun');
            $table->json('competences');                // ["php","laravel"]
            $table->json('langues')->nullable();        // ["français","anglais"]

            // Liens
            $table->string('cv_url')->nullable();
            $table->string('portfolio_url')->nullable();

            // Résultats (remplis par les modules Qualification / Scoring / Ranking)
            $table->boolean('qualifie')->nullable();    // null = pas encore évalué
            $table->string('motif_rejet')->nullable();
            $table->decimal('score', 5, 1)->nullable(); // 0 à 100
            $table->json('score_detail')->nullable();  // {"competences":42.5,"experience":15,...}
            $table->enum('categorie', ['prioritaire', 'a_examiner', 'faible', 'rejete'])
                ->nullable();
            $table->timestamp('evalue_le')->nullable();

            $table->timestamps();

            // Un même email ne postule qu'une fois au même poste
            $table->unique(['poste_id', 'email']);

            // Accélère le classement par poste / catégorie / score
            $table->index(['poste_id', 'categorie', 'score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
