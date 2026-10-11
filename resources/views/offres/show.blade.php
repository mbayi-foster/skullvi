<x-public-layout :title="$poste->titre">
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $poste->titre }}
            </h1>

            <a href="{{ route('candidatures.create', $poste) }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Postuler à cette offre') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status') === 'candidature-envoyee')
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-300">
                    {{ __('Votre candidature a bien été enregistrée.') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <dl class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div class="px-6 py-4 flex flex-wrap items-center gap-3">
                        <span class="inline-flex items-center rounded-full bg-indigo-50 dark:bg-indigo-900/50 px-2.5 py-0.5 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                            {{ ucfirst($poste->niveau_etudes_min) }}
                        </span>

                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Expérience :') }} {{ $poste->experience_min }}–{{ $poste->experience_ideale }} {{ __('ans') }}
                        </span>
                    </div>

                    <div class="px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Description') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $poste->description ?: __('Aucune description.') }}</dd>
                    </div>

                    <div class="px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Compétences requises') }}</dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            @forelse ($poste->competences_requises ?? [] as $competence)
                                <span class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-900/50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300">{{ $competence }}</span>
                            @empty
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Aucune compétence renseignée.') }}</span>
                            @endforelse
                        </dd>
                    </div>

                    <div class="px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Langues attendues') }}</dt>
                        <dd class="mt-2 flex flex-wrap gap-2">
                            @forelse ($poste->langues_attendues ?? [] as $langue)
                                <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">{{ $langue }}</span>
                            @empty
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Aucune langue exigée.') }}</span>
                            @endforelse
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:underline">
                    &larr; {{ __("Toutes les offres") }}
                </a>

                <a href="{{ route('candidatures.create', $poste) }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    {{ __('Postuler à cette offre') }}
                </a>
            </div>
        </div>
    </div>
</x-public-layout>
