<?php

use App\Models\Candidate;
use App\Models\Poste;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('public');

    $this->poste = Poste::factory()->create(['titre' => 'Développeur Laravel']);

    $this->candidaturePayload = [
        'nom' => 'Alice Martin',
        'email' => 'alice@example.com',
        'telephone' => '+33 6 12 34 56 78',
        'annees_experience' => 3.5,
        'niveau_etudes' => 'master',
        'competences' => ['php', 'laravel'],
        'langues' => ['français', 'anglais'],
        'portfolio_url' => 'https://example.com',
    ];
});

test('the application form is available to a visitor', function () {
    $this->get(route('candidatures.create', $this->poste))
        ->assertOk()
        ->assertSee('Développeur Laravel')
        ->assertSee('Envoyer ma candidature');
});

test('a visitor can apply to an active offer', function () {
    $response = $this->post(route('candidatures.store', $this->poste), $this->candidaturePayload);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('offres.show', $this->poste));

    $candidate = Candidate::sole();

    expect($candidate->poste->is($this->poste))->toBeTrue()
        ->and($candidate->nom)->toBe('Alice Martin')
        ->and($candidate->email)->toBe('alice@example.com')
        ->and($candidate->telephone)->toBe('+33 6 12 34 56 78')
        ->and($candidate->niveau_etudes)->toBe('master')
        ->and($candidate->competences)->toBe(['php', 'laravel'])
        ->and($candidate->langues)->toBe(['français', 'anglais'])
        ->and($candidate->cv_path)->toBeNull();
});

test('a visitor can upload a CV file with the application', function () {
    $response = $this->post(route('candidatures.store', $this->poste), array_merge($this->candidaturePayload, [
        'cv' => UploadedFile::fake()->create('cv-alice.pdf', 512, 'application/pdf'),
    ]));

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('offres.show', $this->poste));

    $candidate = Candidate::sole();

    expect($candidate->cv_path)->toStartWith('cvs/')
        ->and($candidate->cv_path)->toEndWith('.pdf');

    Storage::disk('public')->assertExists($candidate->cv_path);
});

test('a rejected application does not store the uploaded CV', function () {
    $this->post(route('candidatures.store', $this->poste), array_merge($this->candidaturePayload, [
        'email' => 'pas-un-email',
        'cv' => UploadedFile::fake()->create('cv-alice.pdf', 512, 'application/pdf'),
    ]))->assertSessionHasErrors('email');

    expect(Candidate::count())->toBe(0)
        ->and(Storage::disk('public')->allFiles('cvs'))->toBe([]);
});

test('the email is normalized and the repeatable lists are cleaned', function () {
    $this->post(route('candidatures.store', $this->poste), array_merge($this->candidaturePayload, [
        'email' => '  Alice@Example.COM ',
        'competences' => ['php', '  ', 'laravel'],
        'langues' => [''],
    ]))->assertSessionHasNoErrors();

    $candidate = Candidate::sole();

    expect($candidate->email)->toBe('alice@example.com')
        ->and($candidate->competences)->toBe(['php', 'laravel'])
        ->and($candidate->langues)->toBe([]);
});

test('the same email cannot apply twice to the same offer', function () {
    $this->poste->candidates()->create([
        'nom' => 'Alice Martin',
        'email' => 'alice@example.com',
        'competences' => ['php'],
    ]);

    $this->post(route('candidatures.store', $this->poste), $this->candidaturePayload)
        ->assertSessionHasErrors('email');

    expect(Candidate::count())->toBe(1);
});

test('the same email can apply to another offer', function () {
    $otherPoste = Poste::factory()->create();

    $this->post(route('candidatures.store', $this->poste), $this->candidaturePayload)
        ->assertSessionHasNoErrors();

    $this->post(route('candidatures.store', $otherPoste), $this->candidaturePayload)
        ->assertSessionHasNoErrors();

    expect(Candidate::count())->toBe(2);
});

test('the application is validated', function (array $payload, string $invalidField) {
    $this->post(route('candidatures.store', $this->poste), array_merge($this->candidaturePayload, $payload))
        ->assertSessionHasErrors($invalidField);

    expect(Candidate::count())->toBe(0);
})->with([
    'missing name' => [['nom' => ''], 'nom'],
    'invalid email' => [['email' => 'pas-un-email'], 'email'],
    'phone too long' => [['telephone' => str_repeat('9', 31)], 'telephone'],
    'missing skills' => [['competences' => []], 'competences'],
    'duplicate skills' => [['competences' => ['php', 'php']], 'competences.1'],
    'unknown study level' => [['niveau_etudes' => 'phd'], 'niveau_etudes'],
    'negative experience' => [['annees_experience' => -1], 'annees_experience'],
    'cv not a document' => [['cv' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain')], 'cv'],
    'cv with a forbidden extension' => [['cv' => UploadedFile::fake()->create('cv.pdf.php', 10, 'application/pdf')], 'cv'],
    'cv too large' => [['cv' => UploadedFile::fake()->create('cv.pdf', 6000, 'application/pdf')], 'cv'],
    'invalid portfolio url' => [['portfolio_url' => 'pas-une-url'], 'portfolio_url'],
]);

test('a visitor cannot apply to a closed offer', function () {
    $poste = Poste::factory()->create(['actif' => false]);

    $this->post(route('candidatures.store', $poste), $this->candidaturePayload)
        ->assertNotFound();

    expect(Candidate::count())->toBe(0);
});
