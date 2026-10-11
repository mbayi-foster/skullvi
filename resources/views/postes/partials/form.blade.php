{{--
    Champs du formulaire d'un poste, alignés sur les colonnes de la table `postes`.
    Attend `$poste` (null en création) et doit être placé dans une balise <form>.
--}}
@php
    $competences = array_values((array) old('competences_requises', $poste?->competences_requises ?? [])) ?: [''];
    $langues = array_values((array) old('langues_attendues', $poste?->langues_attendues ?? [])) ?: [''];
@endphp
<div class="space-y-6">
    <div>
        <x-input-label for="titre" :value="__('Titre')" />
        <x-text-input id="titre" name="titre" type="text" class="mt-1 block w-full" :value="old('titre', $poste?->titre)" required autofocus />
        <x-input-error class="mt-2" :messages="$errors->get('titre')" />
    </div>

    <div>
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" rows="4"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('description', $poste?->description) }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div
        x-data="{
            items: @js($competences),
            add() {
                this.items.push('');
            },
            remove(index) {
                this.items.splice(index, 1);
            },
        }"
    >
        <x-input-label :value="__('Compétences requises')" />
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Au moins une compétence est nécessaire pour qualifier les candidatures.") }}
        </p>

        <div class="mt-2 space-y-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex items-center gap-2">
                    <input type="text" :name="`competences_requises[${index}]`" x-model="items[index]"
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <x-secondary-button type="button" class="shrink-0" @click="remove(index)" x-show="items.length > 1">
                        {{ __('Retirer') }}
                    </x-secondary-button>
                </div>
            </template>
        </div>

        <x-secondary-button type="button" class="mt-2" @click="add()">
            {{ __('Ajouter une compétence') }}
        </x-secondary-button>

        <x-input-error class="mt-2" :messages="$errors->get('competences_requises')" />
        @foreach ($errors->get('competences_requises.*') as $messages)
            <x-input-error class="mt-2" :messages="$messages" />
        @endforeach
    </div>

    <div
        x-data="{
            items: @js($langues),
            add() {
                this.items.push('');
            },
            remove(index) {
                this.items.splice(index, 1);
            },
        }"
    >
        <x-input-label :value="__('Langues attendues')" />
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Facultatif : laissez vide si aucune langue particulière n'est exigée.") }}
        </p>

        <div class="mt-2 space-y-2">
            <template x-for="(item, index) in items" :key="index">
                <div class="flex items-center gap-2">
                    <input type="text" :name="`langues_attendues[${index}]`" x-model="items[index]"
                        class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <x-secondary-button type="button" class="shrink-0" @click="remove(index)">
                        {{ __('Retirer') }}
                    </x-secondary-button>
                </div>
            </template>
        </div>

        <x-secondary-button type="button" class="mt-2" @click="add()">
            {{ __('Ajouter une langue') }}
        </x-secondary-button>

        <x-input-error class="mt-2" :messages="$errors->get('langues_attendues')" />
        @foreach ($errors->get('langues_attendues.*') as $messages)
            <x-input-error class="mt-2" :messages="$messages" />
        @endforeach
    </div>

    <div class="grid gap-6 sm:grid-cols-2">
        <div>
            <x-input-label for="experience_min" :value="__('Expérience minimale (années)')" />
            <x-text-input id="experience_min" name="experience_min" type="number" step="0.1" min="0" max="99.9"
                class="mt-1 block w-full" :value="old('experience_min', $poste?->experience_min ?? 0)" required />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __("Seuil éliminatoire : en dessous, la candidature est rejetée.") }}
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('experience_min')" />
        </div>

        <div>
            <x-input-label for="experience_ideale" :value="__('Expérience idéale (années)')" />
            <x-text-input id="experience_ideale" name="experience_ideale" type="number" step="0.1" min="0" max="99.9"
                class="mt-1 block w-full" :value="old('experience_ideale', $poste?->experience_ideale ?? 3)" required />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __("Plafond du score : au-delà, le candidat n'obtient pas plus de points.") }}
            </p>
            <x-input-error class="mt-2" :messages="$errors->get('experience_ideale')" />
        </div>
    </div>

    <div>
        <x-input-label for="niveau_etudes_min" :value="__('Niveau d\'études minimum')" />
        <select id="niveau_etudes_min" name="niveau_etudes_min"
            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
            @foreach (\App\Models\Poste::NIVEAUX_ETUDES as $niveau)
                <option value="{{ $niveau }}" @selected(old('niveau_etudes_min', $poste?->niveau_etudes_min ?? 'aucun') === $niveau)>
                    {{ ucfirst($niveau) }}
                </option>
            @endforeach
        </select>
        <x-input-error class="mt-2" :messages="$errors->get('niveau_etudes_min')" />
    </div>

    <div>
        <label class="flex items-center gap-3">
            <input type="hidden" name="actif" value="0">
            <input type="checkbox" name="actif" value="1" @checked(old('actif', $poste?->actif ?? true))
                class="rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-indigo-600 dark:text-indigo-500 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600">
            <span class="text-sm text-gray-700 dark:text-gray-300">
                {{ __('Poste actif (visible et ouvert aux candidatures)') }}
            </span>
        </label>
        <x-input-error class="mt-2" :messages="$errors->get('actif')" />
    </div>
</div>
