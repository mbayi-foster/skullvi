<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCandidateAssessmentRequest;
use App\Models\Candidate;
use App\Models\Poste;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CandidateController extends Controller
{
    /**
     * Affiche et classe les candidatures reçues pour un poste.
     */
    public function index(Poste $poste): View
    {
        Gate::authorize('view', $poste);

        $poste->loadCount([
            'candidates',
            'candidates as qualified_candidates_count' => fn ($query) => $query->where('qualifie', true),
            'candidates as priority_candidates_count' => fn ($query) => $query->where('categorie', 'prioritaire'),
            'candidates as review_candidates_count' => fn ($query) => $query->where('categorie', 'a_examiner'),
        ]);

        $candidates = $poste->candidates()
            ->orderByRaw("CASE WHEN categorie = 'rejete' THEN 1 ELSE 0 END")
            ->orderByRaw('CASE WHEN score IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('score')
            ->orderBy('created_at')
            ->paginate(15);

        return view('candidatures.index', [
            'poste' => $poste,
            'candidates' => $candidates,
        ]);
    }

    /**
     * Enregistre la qualification et le score d'un candidat.
     */
    public function update(
        UpdateCandidateAssessmentRequest $request,
        Poste $poste,
        Candidate $candidate
    ): RedirectResponse {
        $attributes = $request->validated();
        $qualifie = match ($attributes['qualifie'] ?? null) {
            '1' => true,
            '0' => false,
            default => null,
        };
        $score = isset($attributes['score']) ? (int) $attributes['score'] : null;

        $candidate->qualifie = $qualifie;
        $candidate->score = $score;
        $candidate->motif_rejet = $qualifie === false ? $attributes['motif_rejet'] : null;
        $candidate->categorie = match (true) {
            $qualifie === false => 'rejete',
            $score === null => null,
            $score >= 75 => 'prioritaire',
            $score >= 50 => 'a_examiner',
            default => 'faible',
        };
        $candidate->evalue_le = $qualifie !== null || $score !== null ? now() : null;
        $candidate->save();

        return redirect()
            ->route('postes.candidatures.index', $poste)
            ->with('status', 'candidature-evaluee');
    }
}
