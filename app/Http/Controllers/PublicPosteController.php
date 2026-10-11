<?php

namespace App\Http\Controllers;

use App\Models\Poste;
use Illuminate\View\View;

class PublicPosteController extends Controller
{
    /**
     * Liste les offres ouvertes aux candidatures.
     */
    public function index(): View
    {
        $postes = Poste::query()
            ->actif()
            ->latest()
            ->paginate(9);

        return view('offres.index', ['postes' => $postes]);
    }

    /**
     * Affiche le détail d'une offre ouverte aux candidatures.
     */
    public function show(Poste $poste): View
    {
        abort_unless($poste->actif, 404);

        return view('offres.show', ['poste' => $poste]);
    }
}
