@php
    $competences = array_values((array) old('competences', [])) ?: [''];
    $langues = array_values((array) old('langues', [])) ?: [''];
@endphp

<x-public-layout :title="__('Postuler').' — '.$poste->titre">
    <x-slot name="header">
        <div class="space-y-1">
            <h1 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Postuler') }} : {{ $poste->titre }}
            </h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                {{ __('Renseignez votre profil pour postuler à cette offre.') }}
            </p>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h2 class="font-semibold text-gray-900 dark:text-gray-100">{{ __("Critères de l'offre") }}</h2>

                <ul class="mt-3 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                    <li>
                        {{ __('Niveau d\'études minimum') }} :
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ ucfirst($poste->niveau_etudes_min) }}</span>
                    </li>
                    <li>
                        {{ __('Expérience souhaitée') }} :
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ $poste->experience_min }}–{{ $poste->experience_ideale }} {{ __('ans') }}</span>
                    </li>
                    <li>
                        {{ __('Compétences attendues') }} :
                        <span class="font-medium text-gray-900 dark:text-gray-100">{{ implode(', ', $poste->competences_requises ?? []) ?: __('Aucune') }}</span>
                    </li>
                </ul>
            </div>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('candidatures.store', $poste) }}" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="nom" :value="__('Nom complet')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom')" required autofocus autocomplete="name" />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required autocomplete="email" />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="telephone" :value="__('Téléphone')" />
                            <x-text-input id="telephone" name="telephone" type="text" class="mt-1 block w-full" :value="old('telephone')" autocomplete="tel" />
                            <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                        </div>

                        <div>
                            <x-input-label for="annees_experience" :value="__('Années d\'expérience')" />
                            <x-text-input id="annees_experience" name="annees_experience" type="number" step="0.1" min="0" max="99.9"
                                class="mt-1 block w-full" :value="old('annees_experience', 0)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('annees_experience')" />
                        </div>
                    </div>

                    <div>
                        <x-input-label for="niveau_etudes" :value="__('Niveau d\'études')" />
                        <select id="niveau_etudes" name="niveau_etudes"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            @foreach (\App\Models\Poste::NIVEAUX_ETUDES as $niveau)
                                <option value="{{ $niveau }}" @selected(old('niveau_etudes', 'aucun') === $niveau)>
                                    {{ ucfirst($niveau) }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('niveau_etudes')" />
                    </div>

                    <div>
                        <x-input-label :value="__('Vos compétences')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __('Indiquez les compétences que vous maîtrisez ; elles sont comparées aux compétences attendues.') }}
                        </p>

                        <x-dynamic-list name="competences" :items="$competences" :add-label="__('Ajouter une compétence')" />

                        <x-input-error class="mt-2" :messages="$errors->get('competences')" />
                        @foreach ($errors->get('competences.*') as $messages)
                            <x-input-error class="mt-2" :messages="$messages" />
                        @endforeach
                    </div>

                    <div>
                        <x-input-label :value="__('Vos langues')" />
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                            {{ __("Facultatif : laissez vide si aucune langue n'est à signaler.") }}
                        </p>

                        <x-dynamic-list name="langues" :items="$langues" :add-label="__('Ajouter une langue')" />

                        <x-input-error class="mt-2" :messages="$errors->get('langues')" />
                        @foreach ($errors->get('langues.*') as $messages)
                            <x-input-error class="mt-2" :messages="$messages" />
                        @endforeach
                    </div>

                    <div class="grid gap-6 sm:grid-cols-2">
                        <div>
                            <x-input-label for="cv" :value="__('Votre CV')" />
                            <input id="cv" name="cv" type="file" accept=".pdf,.doc,.docx"
                                class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm file:mr-4 file:rounded-md file:border-0 file:bg-gray-800 file:px-4 file:py-2 file:text-xs file:font-semibold file:uppercase file:tracking-widest file:text-white hover:file:bg-gray-700 dark:file:bg-gray-200 dark:file:text-gray-800 dark:hover:file:bg-white">
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ __('Formats acceptés : PDF, DOC, DOCX — 5 Mo maximum.') }}
                            </p>
                            <x-input-error class="mt-2" :messages="$errors->get('cv')" />
                        </div>

                        <div>
                            <x-input-label for="portfolio_url" :value="__('Lien vers votre portfolio')" />
                            <x-text-input id="portfolio_url" name="portfolio_url" type="url" class="mt-1 block w-full" :value="old('portfolio_url')" placeholder="https://" />
                            <x-input-error class="mt-2" :messages="$errors->get('portfolio_url')" />
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <x-primary-button>{{ __('Envoyer ma candidature') }}</x-primary-button>

                        <a href="{{ route('offres.show', $poste) }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 hover:underline">
                            {{ __('Retour à l\'offre') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-public-layout>
