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
        Schema::create('postes', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description')->nullable();

            // Critères utilisés par la qualification et le scoring
            $table->json('competences_requises');                 // ["php","laravel","mysql"]
            $table->json('langues_attendues')->nullable();        // ["français","anglais"]
            $table->decimal('experience_min', 4, 1)->default(0);   // seuil éliminatoire
            $table->decimal('experience_ideale', 4, 1)->default(3); // plafond du score
            $table->enum('niveau_etudes_min', ['aucun', 'bac', 'licence', 'master', 'doctorat'])
                ->default('aucun');

            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postes');
    }
};
