<?php

use App\Models\Candidate;
use App\Models\Poste;
use App\Models\User;

function createCandidateForAssessment(Poste $poste, string $name): Candidate
{
    return $poste->candidates()->create([
        'nom' => $name,
        'email' => str_replace(' ', '.', strtolower($name)).'@example.com',
        'annees_experience' => 3,
        'niveau_etudes' => 'licence',
        'competences' => ['php'],
    ]);
}

test('guests cannot view or assess applications', function () {
    $poste = Poste::factory()->create();
    $candidate = createCandidateForAssessment($poste, 'Alice Martin');

    $this->get(route('postes.candidatures.index', $poste))
        ->assertRedirect(route('login'));

    $this->patch(route('postes.candidatures.update', [$poste, $candidate]), [
        'qualifie' => '1',
        'score' => 80,
    ])->assertRedirect(route('login'));

    expect($candidate->fresh()->score)->toBeNull();
});

test('the owner can see only their applications sorted by priority', function () {
    $owner = User::factory()->create();
    $poste = Poste::factory()->for($owner)->create();
    $priority = createCandidateForAssessment($poste, 'Alice Prioritaire');
    $priority->score = 90;
    $priority->categorie = 'prioritaire';
    $priority->save();

    $review = createCandidateForAssessment($poste, 'Benoit A Examiner');
    $review->score = 60;
    $review->categorie = 'a_examiner';
    $review->save();

    $unscored = createCandidateForAssessment($poste, 'Chloe Non Notee');

    $rejected = createCandidateForAssessment($poste, 'David Rejete');
    $rejected->qualifie = false;
    $rejected->score = 100;
    $rejected->categorie = 'rejete';
    $rejected->save();

    $otherPoste = Poste::factory()->create();
    createCandidateForAssessment($otherPoste, 'Profil Etranger');

    $this->actingAs($owner)
        ->get(route('postes.candidatures.index', $poste))
        ->assertOk()
        ->assertSee('À examiner en priorité')
        ->assertSeeInOrder([
            'Alice Prioritaire',
            'Benoit A Examiner',
            'Chloe Non Notee',
            'David Rejete',
        ])
        ->assertDontSee('Profil Etranger');
});

test('a user cannot view another owner’s applications', function () {
    $poste = Poste::factory()->create();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('postes.candidatures.index', $poste))
        ->assertForbidden();
});

test('a candidate cannot be assessed through a different post route', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();
    $otherPoste = Poste::factory()->for($user)->create();
    $candidate = createCandidateForAssessment($otherPoste, 'Alice Martin');

    $this->actingAs($user)
        ->patch(route('postes.candidatures.update', [$poste, $candidate]), [
            'qualifie' => '1',
            'score' => 80,
        ])
        ->assertNotFound();

    expect($candidate->fresh()->score)->toBeNull();
});

test('scores determine candidate categories at the defined thresholds', function (?int $score, ?string $category) {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();
    $candidate = createCandidateForAssessment($poste, 'Alice Martin');

    $this->actingAs($user)
        ->patch(route('postes.candidatures.update', [$poste, $candidate]), [
            'qualifie' => '1',
            'score' => $score,
        ])
        ->assertRedirect(route('postes.candidatures.index', $poste))
        ->assertSessionHas('status', 'candidature-evaluee');

    $candidate->refresh();

    expect($candidate->qualifie)->toBeTrue()
        ->and($candidate->categorie)->toBe($category)
        ->and($candidate->evalue_le)->not->toBeNull();

    if ($score !== null) {
        expect((int) $candidate->score)->toBe($score);
    } else {
        expect($candidate->score)->toBeNull();
    }
})->with([
    'no score' => [null, null],
    'below review threshold' => [49, 'faible'],
    'review threshold' => [50, 'a_examiner'],
    'below priority threshold' => [74, 'a_examiner'],
    'priority threshold' => [75, 'prioritaire'],
    'maximum score' => [100, 'prioritaire'],
]);

test('an empty assessment does not appear as evaluated', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();
    $candidate = createCandidateForAssessment($poste, 'Alice Martin');

    $this->actingAs($user)
        ->patch(route('postes.candidatures.update', [$poste, $candidate]), [
            'qualifie' => '',
            'score' => '',
        ])
        ->assertRedirect(route('postes.candidatures.index', $poste));

    expect($candidate->fresh()->evalue_le)->toBeNull();
});

test('a rejected candidate requires a reason and is excluded from score categories', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();
    $candidate = createCandidateForAssessment($poste, 'Alice Martin');

    $this->actingAs($user)
        ->patch(route('postes.candidatures.update', [$poste, $candidate]), [
            'qualifie' => '0',
            'score' => 90,
        ])
        ->assertSessionHasErrors('motif_rejet');

    expect($candidate->fresh()->categorie)->toBeNull();

    $this->patch(route('postes.candidatures.update', [$poste, $candidate]), [
        'qualifie' => '0',
        'score' => 90,
        'motif_rejet' => 'Expérience insuffisante pour ce poste.',
    ])->assertRedirect(route('postes.candidatures.index', $poste));

    $candidate->refresh();

    expect($candidate->qualifie)->toBeFalse()
        ->and($candidate->categorie)->toBe('rejete')
        ->and($candidate->motif_rejet)->toBe('Expérience insuffisante pour ce poste.');
});

test('candidate scores must be integers between zero and one hundred', function (array $payload, string $field) {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();
    $candidate = createCandidateForAssessment($poste, 'Alice Martin');

    $this->actingAs($user)
        ->patch(route('postes.candidatures.update', [$poste, $candidate]), $payload)
        ->assertSessionHasErrors($field);

    expect($candidate->fresh()->score)->toBeNull();
})->with([
    'negative score' => [['qualifie' => '1', 'score' => -1], 'score'],
    'score above maximum' => [['qualifie' => '1', 'score' => 101], 'score'],
    'decimal score' => [['qualifie' => '1', 'score' => 75.5], 'score'],
]);

test('candidate category cannot be set directly in the request', function () {
    $user = User::factory()->create();
    $poste = Poste::factory()->for($user)->create();
    $candidate = createCandidateForAssessment($poste, 'Alice Martin');

    $this->actingAs($user)
        ->patch(route('postes.candidatures.update', [$poste, $candidate]), [
            'qualifie' => '1',
            'score' => 20,
            'categorie' => 'prioritaire',
        ])
        ->assertRedirect(route('postes.candidatures.index', $poste));

    expect($candidate->fresh()->categorie)->toBe('faible');
});
