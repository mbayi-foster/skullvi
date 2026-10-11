<?php

namespace App\Models;

use App\Policies\PostePolicy;
use Database\Factories\PosteFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'titre',
    'description',
    'competences_requises',
    'langues_attendues',
    'experience_min',
    'experience_ideale',
    'niveau_etudes_min',
    'actif',
])]
#[UsePolicy(PostePolicy::class)]
class Poste extends Model
{
    /** @use HasFactory<PosteFactory> */
    use HasFactory;

    /**
     * Niveaux d'études acceptés par les colonnes `niveau_etudes_min` et `niveau_etudes`.
     *
     * @var list<string>
     */
    public const NIVEAUX_ETUDES = ['aucun', 'bac', 'licence', 'master', 'doctorat'];

    /**
     * Recruteur à l'origine du poste.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Candidatures reçues pour ce poste.
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(Candidate::class);
    }

    /**
     * Limite la requête aux postes ouverts aux candidatures.
     */
    #[Scope]
    protected function actif(Builder $query): Builder
    {
        return $query->where('actif', true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'competences_requises' => 'array',
            'langues_attendues' => 'array',
            'actif' => 'boolean',
        ];
    }
}
