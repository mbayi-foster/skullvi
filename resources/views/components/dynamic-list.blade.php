{{--
    Répéteur de champs texte piloté par Alpine.
    Attend `name` (nom de base des champs, envoyés en tableau) et `items` (valeurs initiales).
--}}
@props([
    'name',
    'items' => [],
    'addLabel' => null,
])

@php
    $initialItems = array_values((array) $items) ?: [''];
@endphp

<div
    x-data="{
        items: @js($initialItems),
        add() {
            this.items.push('');
        },
        remove(index) {
            this.items.splice(index, 1);
        },
    }"
>
    <div class="mt-2 space-y-2">
        <template x-for="(item, index) in items" :key="index">
            <div class="flex items-center gap-2">
                <input
                    type="text"
                    :name="`{{ $name }}[${index}]`"
                    x-model="items[index]"
                    {{ $attributes->merge(['class' => 'block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm']) }}
                >
                <x-secondary-button type="button" class="shrink-0" @click="remove(index)" x-show="items.length > 1">
                    {{ __('Retirer') }}
                </x-secondary-button>
            </div>
        </template>
    </div>

    <x-secondary-button type="button" class="mt-2" @click="add()">
        {{ $addLabel ?? __('Ajouter') }}
    </x-secondary-button>
</div>
