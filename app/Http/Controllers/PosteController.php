<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePosteRequest;
use App\Http\Requests\UpdatePosteRequest;
use App\Models\Poste;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PosteController extends Controller
{
    /**
     * Liste les postes créés par l'utilisateur connecté.
     */
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Poste::class);

        $postes = $request->user()
            ->postes()
            ->withCount('candidates')
            ->latest()
            ->paginate(10);

        return view('postes.index', ['postes' => $postes]);
    }

    /**
     * Affiche le formulaire de création d'un poste.
     */
    public function create(): View
    {
        Gate::authorize('create', Poste::class);

        return view('postes.create');
    }

    /**
     * Enregistre un nouveau poste rattaché à l'utilisateur connecté.
     */
    public function store(StorePosteRequest $request): RedirectResponse
    {
        $poste = $request->user()->postes()->create($request->validated());

        return redirect()
            ->route('postes.show', $poste)
            ->with('status', 'poste-created');
    }

    /**
     * Affiche le détail d'un poste.
     */
    public function show(Poste $poste): View
    {
        Gate::authorize('view', $poste);

        $poste->loadCount('candidates');

        return view('postes.show', ['poste' => $poste]);
    }

    /**
     * Affiche le formulaire de modification d'un poste.
     */
    public function edit(Poste $poste): View
    {
        Gate::authorize('update', $poste);

        return view('postes.edit', ['poste' => $poste]);
    }

    /**
     * Met à jour un poste existant.
     */
    public function update(UpdatePosteRequest $request, Poste $poste): RedirectResponse
    {
        $poste->update($request->validated());

        return redirect()
            ->route('postes.show', $poste)
            ->with('status', 'poste-updated');
    }

    /**
     * Supprime un poste sans candidature.
     */
    public function destroy(Poste $poste): RedirectResponse
    {
        Gate::authorize('delete', $poste);

        if ($poste->candidates()->exists()) {
            return redirect()
                ->route('postes.show', $poste)
                ->with('error', __('Ce poste a reçu des candidatures et ne peut pas être supprimé.'));
        }

        $poste->delete();

        return redirect()
            ->route('postes.index')
            ->with('status', 'poste-deleted');
    }
}
