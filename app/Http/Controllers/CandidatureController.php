<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCandidatureRequest;
use App\Models\Poste;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CandidatureController extends Controller
{
    /**
     * Affiche le formulaire de candidature d'une offre.
     */
    public function create(Poste $poste): View
    {
        abort_unless($poste->actif, 404);

        return view('candidatures.create', ['poste' => $poste]);
    }

    /**
     * Enregistre la candidature d'un visiteur pour une offre.
     */
    public function store(StoreCandidatureRequest $request, Poste $poste): RedirectResponse
    {
        abort_unless($poste->actif, 404);

        $attributes = $request->safe()->except('cv');
        $attributes['cv_path'] = $request->file('cv')?->store('cvs', 'public');

        $poste->candidates()->create($attributes);

        return redirect()
            ->route('offres.show', $poste)
            ->with('status', 'candidature-envoyee');
    }
}
