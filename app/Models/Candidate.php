<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Candidate extends Model
{
    //
    protected $casts = [
        'competences' => 'array',
        'langues' => 'array',
        'score_detail' => 'array',
        'qualifie' => 'boolean',
        'evalue_le' => 'datetime',
    ];

    /**
     * Poste auquel la candidature répond.
     */
    public function poste(): BelongsTo
    {
        return $this->belongsTo(Poste::class);
    }
}
