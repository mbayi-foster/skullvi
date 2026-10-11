<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mes postes') }}
            </h2>

            <a href="{{ route('postes.create') }}"
                class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                {{ __('Nouveau poste') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="rounded-md bg-green-50 dark:bg-green-900/30 px-4 py-3 text-sm font-medium text-green-700 dark:text-green-300">
                    {{ match (session('status')) {
                        'poste-created' => __('Poste créé.'),
                        'poste-updated' => __('Poste mis à jour.'),
                        'poste-deleted' => __('Poste supprimé.'),
                        default => __('Opération effectuée.'),
                    } }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 dark:bg-red-900/30 px-4 py-3 text-sm font-medium text-red-700 dark:text-red-300">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                @if ($postes->isEmpty())
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        {{ __("Vous n'avez pas encore créé de poste.") }}
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Titre') }}</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Niveau requis') }}</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Expérience') }}</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Candidatures') }}</th>
                                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Statut') }}</th>
                                    <th scope="col" class="px-6 py-3 text-end text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($postes as $poste)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium">
                                            <a href="{{ route('postes.show', $poste) }}" class="text-gray-900 dark:text-gray-100 hover:text-indigo-600 dark:hover:text-indigo-400 hover:underline">
                                                {{ $poste->titre }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ ucfirst($poste->niveau_etudes_min) }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $poste->experience_min }} – {{ $poste->experience_ideale }} {{ __('ans') }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $poste->candidates_count }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            @if ($poste->actif)
                                                <span class="inline-flex items-center rounded-full bg-green-100 dark:bg-green-900/50 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:text-green-300">{{ __('Actif') }}</span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-gray-100 dark:bg-gray-700 px-2.5 py-0.5 text-xs font-medium text-gray-700 dark:text-gray-300">{{ __('Inactif') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-end text-sm whitespace-nowrap">
                                            <a href="{{ route('postes.edit', $poste) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">{{ __('Modifier') }}</a>
                                            <form method="POST" action="{{ route('postes.destroy', $poste) }}" class="inline" onsubmit="return confirm('Supprimer définitivement ce poste ?')">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="ms-3 text-red-600 dark:text-red-400 hover:underline">{{ __('Supprimer') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{ $postes->links() }}
        </div>
    </div>
</x-app-layout>
