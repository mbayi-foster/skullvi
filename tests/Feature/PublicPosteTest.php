<?php

use App\Models\Poste;
use App\Models\User;

test('a visitor browses the offers displayed as a grid', function () {
    Poste::factory()->create(['titre' => 'Développeur Laravel']);
    Poste::factory()->create(['titre' => 'Designer UI']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Développeur Laravel')
        ->assertSee('Designer UI')
        ->assertSee('grid gap-6 sm:grid-cols-2 lg:grid-cols-3', false)
        ->assertSee('Postuler');
});

test('the home page guides the visitor through the application steps', function () {
    Poste::factory()->create(['titre' => 'Développeur Laravel']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Comment postuler ?')
        ->assertSee('Choisissez une offre')
        ->assertSee('Préparez votre CV')
        ->assertSee('Remplissez le formulaire')
        ->assertSee('Envoyez votre candidature')
        ->assertSee('Créer un compte recruteur');
});

test('the recruiter call to action is hidden from authenticated recruiters', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('home'))
        ->assertOk()
        ->assertDontSee('Créer un compte recruteur');
});

test('only active offers are listed', function () {
    Poste::factory()->create(['titre' => 'Offre ouverte']);
    Poste::factory()->create(['titre' => 'Offre fermée', 'actif' => false]);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Offre ouverte')
        ->assertDontSee('Offre fermée');
});

test('the offers listing is paginated', function () {
    Poste::factory()->count(10)->create();

    $postes = $this->get(route('home'))->assertOk()->viewData('postes');

    expect($postes->perPage())->toBe(9)
        ->and($postes->total())->toBe(10)
        ->and($postes->count())->toBe(9);
});

test('a visitor reads the detail of an active offer', function () {
    $poste = Poste::factory()->create(['titre' => 'Architecte logiciel']);

    $this->get(route('offres.show', $poste))
        ->assertOk()
        ->assertSee('Architecte logiciel')
        ->assertSee('Postuler à cette offre');
});

test('a closed offer is not reachable', function () {
    $poste = Poste::factory()->create(['actif' => false]);

    $this->get(route('offres.show', $poste))->assertNotFound();
    $this->get(route('candidatures.create', $poste))->assertNotFound();
});
