<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <a href="{{ route('postes.show', $poste) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    &larr; {{ __('Retour au poste') }}
                </a>
                <h2 class="mt-1 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    {{ __('Candidatures — :poste', ['poste' => $poste->titre]) }}
                </h2>
            </div>
            <span class="rounded-full bg-indigo-50 dark:bg-indigo-900/50 px-3 py-1 text-sm font-medium text-indigo-700 dark:text-indigo-300">
                {{ trans_choice('{0} :count candidature|{1} :count candidature|[2,*] :count candidatures', $poste->candidates_count, ['count' => $poste->candidates_count]) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @if (session('status') === 'candidature-evaluee')
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-300">
                    {{ __('L’évaluation de la candidature a été enregistrée.') }}
                </div>
            @endif
            @if ($errors->any())
                <div role="alert" class="rounded-md bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm font-medium text-red-700 dark:text-red-300">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Candidatures reçues') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $poste->candidates_count }}</p>
                </div>
                <div class="rounded-lg bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('À examiner en priorité') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $poste->priority_candidates_count }}</p>
                </div>
                <div class="rounded-lg bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('À examiner') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-amber-600 dark:text-amber-400">{{ $poste->review_candidates_count }}</p>
                </div>
                <div class="rounded-lg bg-white dark:bg-gray-800 p-5 shadow-sm">
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Qualifiés') }}</p>
                    <p class="mt-1 text-2xl font-semibold text-green-600 dark:text-green-400">{{ $poste->qualified_candidates_count }}</p>
                </div>
            </div>

            @if ($candidates->isEmpty())
                <div class="rounded-lg bg-white dark:bg-gray-800 p-8 text-center shadow-sm">
                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ __('Aucune candidature pour le moment') }}</h3>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Les candidatures reçues pour ce poste apparaîtront ici.') }}</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($candidates as $candidate)
                        <article class="rounded-lg bg-white dark:bg-gray-800 p-6 shadow-sm">
                            <div class="flex flex-wrap items-start justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-700 text-sm font-semibold text-gray-700 dark:text-gray-200">
                                        {{ $candidates->firstItem() + $loop->index }}
                                    </span>
                                    <div>
                                        <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100">{{ $candidate->nom }}</h3>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $candidate->email }}@if ($candidate->telephone) <span aria-hidden="true">·</span> {{ $candidate->telephone }}@endif</p>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    @if ($candidate->qualifie === true)
                                        <span class="rounded-full bg-green-100 dark:bg-green-900/50 px-3 py-1 text-xs font-medium text-green-800 dark:text-green-300">{{ __('Qualifié') }}</span>
                                    @elseif ($candidate->qualifie === false)
                                        <span class="rounded-full bg-red-100 dark:bg-red-900/50 px-3 py-1 text-xs font-medium text-red-800 dark:text-red-300">{{ __('Rejeté') }}</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('À qualifier') }}</span>
                                    @endif

                                    @if ($candidate->categorie === 'prioritaire')
                                        <span class="rounded-full bg-indigo-100 dark:bg-indigo-900/50 px-3 py-1 text-xs font-medium text-indigo-800 dark:text-indigo-300">{{ __('Prioritaire') }}</span>
                                    @elseif ($candidate->categorie === 'a_examiner')
                                        <span class="rounded-full bg-amber-100 dark:bg-amber-900/50 px-3 py-1 text-xs font-medium text-amber-800 dark:text-amber-300">{{ __('À examiner') }}</span>
                                    @elseif ($candidate->categorie === 'faible')
                                        <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Score faible') }}</span>
                                    @elseif ($candidate->score === null && $candidate->qualifie !== false)
                                        <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Score à attribuer') }}</span>
                                    @endif
                                    @if ($candidate->score !== null)
                                        <span class="rounded-full bg-gray-100 dark:bg-gray-700 px-3 py-1 text-xs font-semibold text-gray-800 dark:text-gray-200">{{ number_format((float) $candidate->score, 0, ',', ' ') }}/100</span>
                                    @endif
                                </div>
                            </div>

                            <dl class="mt-5 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Expérience') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $candidate->annees_experience }} {{ __('ans') }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Études') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ ucfirst($candidate->niveau_etudes) }}</dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Compétences') }}</dt>
                                    <dd class="mt-1 flex flex-wrap gap-1.5">
                                        @forelse ($candidate->competences ?? [] as $competence)
                                            <span class="rounded bg-gray-100 dark:bg-gray-700 px-2 py-0.5 text-xs text-gray-700 dark:text-gray-300">{{ $competence }}</span>
                                        @empty
                                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Non renseignées') }}</span>
                                        @endforelse
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Langues') }}</dt>
                                    <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ implode(', ', $candidate->langues ?? []) ?: __('Non renseignées') }}</dd>
                                </div>
                            </dl>

                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                @if ($candidate->portfolio_url)
                                    <span class="text-gray-600 dark:text-gray-400">{{ __('Portfolio : :url', ['url' => $candidate->portfolio_url]) }}</span>
                                @endif
                                @if ($candidate->cv_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($candidate->cv_path) }}" class="font-medium text-indigo-600 dark:text-indigo-400 hover:underline" target="_blank" rel="noopener noreferrer">
                                        {{ __('Consulter le CV') }}
                                    </a>
                                @endif
                            </div>

                            @if ($candidate->motif_rejet)
                                <p class="mt-4 rounded-md bg-red-50 dark:bg-red-900/20 px-3 py-2 text-sm text-red-700 dark:text-red-300">
                                    <span class="font-medium">{{ __('Motif du rejet :') }}</span> {{ $candidate->motif_rejet }}
                                </p>
                            @endif

                            <form method="POST" action="{{ route('postes.candidatures.update', [$poste, $candidate]) }}" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-5">
                                @csrf
                                @method('PATCH')
                                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        <label for="qualifie-{{ $candidate->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Qualification') }}</label>
                                        <select id="qualifie-{{ $candidate->id }}" name="qualifie" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="" @selected($candidate->qualifie === null)>{{ __('À qualifier') }}</option>
                                            <option value="1" @selected($candidate->qualifie === true)>{{ __('Qualifié') }}</option>
                                            <option value="0" @selected($candidate->qualifie === false)>{{ __('Rejeté') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="score-{{ $candidate->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Score sur 100') }}</label>
                                        <input id="score-{{ $candidate->id }}" name="score" type="number" min="0" max="100" step="1" value="{{ $candidate->score === null ? '' : (int) $candidate->score }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0–100">
                                    </div>
                                    <div class="sm:col-span-2">
                                        <label for="motif-rejet-{{ $candidate->id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Motif du rejet') }}</label>
                                        <input id="motif-rejet-{{ $candidate->id }}" name="motif_rejet" type="text" value="{{ $candidate->motif_rejet }}" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('Obligatoire si la candidature est rejetée') }}">
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center justify-between gap-4">
                                    @if ($candidate->evalue_le)
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Évalué le :date', ['date' => $candidate->evalue_le->format('d/m/Y H:i')]) }}</p>
                                    @else
                                        <span></span>
                                    @endif
                                    <button type="submit" class="inline-flex items-center rounded-md bg-gray-800 dark:bg-gray-200 px-4 py-2 text-sm font-semibold text-white dark:text-gray-800 hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                        {{ __('Enregistrer l’évaluation') }}
                                    </button>
                                </div>
                            </form>
                        </article>
                    @endforeach
                </div>

                {{ $candidates->links() }}
            @endif
        </div>
    </div>
</x-app-layout>
