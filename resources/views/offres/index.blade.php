<x-public-layout :title="__('Offres d\'emploi')">
    <x-slot name="header">
        <div class="flex flex-wrap items-baseline justify-between gap-2">
            <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __("Offres d'emploi") }}
            </h1>

            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ trans_choice('{0} Aucune offre publiée|{1} :count offre publiée|[2,*] :count offres publiées', $postes->total(), ['count' => $postes->total()]) }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if ($postes->isEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900 dark:text-gray-100">
                    {{ __("Aucune offre n'est ouverte aux candidatures pour le moment.") }}
                </div>
            @else
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($postes as $poste)
                        <article class="flex flex-col bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="flex-1 p-6 space-y-4">
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        <a href="{{ route('offres.show', $poste) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">
                                            {{ $poste->titre }}
                                        </a>
                                    </h2>

                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        {{ ucfirst($poste->niveau_etudes_min) }} · {{ $poste->experience_min }}–{{ $poste->experience_ideale }} {{ __('ans') }}
                                    </p>
                                </div>

                                @if ($poste->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ \Illuminate\Support\Str::limit($poste->description, 120) }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-2">
                                    @foreach (array_slice($poste->competences_requises ?? [], 0, 4) as $competence)
                                        <span class="inline-flex items-center rounded-md bg-indigo-50 dark:bg-indigo-900/50 px-2.5 py-1 text-xs font-medium text-indigo-700 dark:text-indigo-300">
                                            {{ $competence }}
                                        </span>
                                    @endforeach

                                    @if (count($poste->competences_requises ?? []) > 4)
                                        <span class="inline-flex items-center rounded-md bg-gray-100 dark:bg-gray-700 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                                            +{{ count($poste->competences_requises) - 4 }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex items-center justify-between gap-3">
                                <a href="{{ route('offres.show', $poste) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:underline">
                                    {{ __('Voir le détail') }}
                                </a>

                                <a href="{{ route('candidatures.create', $poste) }}"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    {{ __('Postuler') }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{ $postes->links() }}
            @endif
        </div>
    </div>
</x-public-layout>
