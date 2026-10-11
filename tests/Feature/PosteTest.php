<?php

use App\Models\Candidate;
use App\Models\Poste;
use App\Models\User;

beforeEach(function () {
    $this->postePayload = [
        'titre' => 'Développeur Laravel',
        'description' => 'Poste de développeur backend.',
        'competences_requises' => ['php', 'laravel'],
        'langues_attendues' => ['français'],
        'experience_min' => 2,
        'experience_ideale' => 5,
        'niveau_etudes_min' => 'licence',
        'actif' => '1',
    ];
});

test('guests are redirected to the login page', function () {
    $poste = Poste::factory()->create();

    $this->get(route('postes.index'))->assertRedirect(route('login'));
    $this->get(route('postes.create'))->assertRedirect(route('login'));
    $this->post(route('postes.store'), $this->postePayload)->assertRedirect(route('login'));
    $this->get(route('postes.show', $poste))->assertRedirect(route('login'));
    $this->get(route('postes.edit', $poste))->assertRedirect(route('login'));
    $this->patch(route('postes.update', $poste), $this->postePayload)->assertRedirect(route('login'));
    $this->delete(route('postes.destroy', $poste))->assertRedirect(route('login'));

    expect(Poste::count())->toBe(1);
});

test('the index only lists the posts of the authenticated user', function () {
    $user = User::factory()->create();
    Poste::factory()->for($user)->create(['titre' => 'Poste personnel']);
    Poste::factory()->create(['titre' => 'Poste étranger']);

    $this->actingAs($user)
        ->get(route('postes.index'))
        ->assertOk()
        ->assertSee('Poste personnel')
        ->assertDontSee('Poste étranger');
});

test('the create page is displayed', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('postes.create'))
        ->assertOk()
        ->assertSee('Compétences requises');
});

test('a post can be created for the authenticated user', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('postes.store'), $this->postePayload);

    $response->assertSessionHasNoErrors();

    $poste = Poste::sole();

    expect($poste->user->is($user))->toBeTrue()
        ->and($poste->titre)->toBe('Développeur Laravel')
        ->and($poste->competences_requises)->toBe(['php', 'laravel'])
        ->and($poste->langues_attendues)->toBe(['français'])
        ->and($poste->niveau_etudes_min)->toBe('licence')
        ->and($poste->actif)->toBeTrue();

    $response->assertRedirect(route('postes.show', $poste));
});

test('blank skill and language rows are ignored', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('postes.store'), array_merge($this->postePayload, [
            'competences_requises' => ['php', '  ', ''],
            'langues_attendues' => [''],
        ]))
        ->assertSessionHasNoErrors();

    $poste = Poste::sole();

    expect($poste->competences_requises)->toBe(['php'])
        ->and($poste->langues_attendues)->toBe([]);
});

test('the post data is validated', function (array $payload, string $invalidField) {
    $this->actingAs(User::factory()->create())
        ->post(route('postes.store'), array_merge($this->postePayload, $payload))
        ->assertSessionHasErrors($invalidField);

    expect(Poste::count())->toBe(0);
})->with([
    'missing title' => [['titre' => ''], 'titre'],
    'no skill' => [['competences_requises' => []], 'competences_requises'],
    'duplicate skills' => [['competences_requises' => ['php', 'php']], 'competences_requises.1'],
    'threshold above ideal experience' => [['experience_min' => 6, 'experience_ideale' => 2], 'experience_ideale'],
    'unknown study level' => [['niveau_etudes_min' => 'phd'], 'niveau_etudes_min'],
    'negative experience' => [['experience_min' => -1], 'experience_min'],
]);

test('the owner can view the post detail', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create(['titre' => 'Architecte logiciel']);

    $this->actingAs($user)
        ->get(route('postes.show', $poste))
        ->assertOk()
        ->assertSee('Architecte logiciel');
});

test('the edit page is displayed with the post values', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create([
        'titre' => 'Ingénieur données',
        'competences_requises' => ['python', 'sql'],
        'langues_attendues' => ['anglais'],
    ]);

    $this->actingAs($user)
        ->get(route('postes.edit', $poste))
        ->assertOk()
        ->assertSee('Ingénieur données')
        ->assertSee('python')
        ->assertSee('sql')
        ->assertSee('anglais');
});

test('a user cannot manage the posts of another user', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();
    $poste = Poste::factory()->for($owner)->create(['titre' => 'Poste confidentiel']);

    $this->actingAs($intruder)->get(route('postes.show', $poste))->assertForbidden();
    $this->actingAs($intruder)->get(route('postes.edit', $poste))->assertForbidden();
    $this->actingAs($intruder)->patch(route('postes.update', $poste), $this->postePayload)->assertForbidden();
    $this->actingAs($intruder)->delete(route('postes.destroy', $poste))->assertForbidden();

    expect($poste->fresh()->titre)->toBe('Poste confidentiel');
});

test('an orphaned post cannot be managed', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->create(['user_id' => null]);

    $this->actingAs($user)->get(route('postes.show', $poste))->assertForbidden();
    $this->actingAs($user)->delete(route('postes.destroy', $poste))->assertForbidden();

    expect($poste->fresh())->not->toBeNull();
});

test('a post can be updated by its owner', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();

    $this->actingAs($user)
        ->patch(route('postes.update', $poste), array_merge($this->postePayload, [
            'titre' => 'Lead développeur',
            'competences_requises' => ['php', 'laravel', 'docker'],
            'actif' => '0',
        ]))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('postes.show', $poste));

    $poste->refresh();

    expect($poste->titre)->toBe('Lead développeur')
        ->and($poste->competences_requises)->toBe(['php', 'laravel', 'docker'])
        ->and($poste->actif)->toBeFalse();
});

test('a post can be deleted by its owner', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();

    $this->actingAs($user)
        ->delete(route('postes.destroy', $poste))
        ->assertRedirect(route('postes.index'));

    expect(Poste::count())->toBe(0);
});

test('a post with applications cannot be deleted', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();

    // Candidate n'expose pas encore de liste $fillable : on force l'insertion du jeu de test.
    $poste->candidates()->forceCreate([
        'nom' => 'Alice Martin',
        'email' => 'alice@example.com',
        'annees_experience' => 3,
        'niveau_etudes' => 'master',
        'competences' => ['php', 'laravel'],
    ]);

    $this->actingAs($user)
        ->delete(route('postes.destroy', $poste))
        ->assertRedirect(route('postes.show', $poste));

    expect($poste->fresh())->not->toBeNull()
        ->and(Candidate::count())->toBe(1);
});
