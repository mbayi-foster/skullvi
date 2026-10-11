<?php

use App\Models\Poste;

test('a visitor browses the offers displayed as a grid', function () {
    Poste::factory()->create(['titre' => 'Développeur Laravel']);
    Poste::factory()->create(['titre' => 'Designer UI']);

    $this->get(route('offres.index'))
        ->assertOk()
        ->assertSee('Développeur Laravel')
        ->assertSee('Designer UI')
        ->assertSee('grid gap-6 sm:grid-cols-2 lg:grid-cols-3', false)
        ->assertSee('Postuler');
});

test('only active offers are listed', function () {
    Poste::factory()->create(['titre' => 'Offre ouverte']);
    Poste::factory()->create(['titre' => 'Offre fermée', 'actif' => false]);

    $this->get(route('offres.index'))
        ->assertOk()
        ->assertSee('Offre ouverte')
        ->assertDontSee('Offre fermée');
});

test('the offers listing is paginated', function () {
    Poste::factory()->count(10)->create();

    $postes = $this->get(route('offres.index'))->assertOk()->viewData('postes');

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
