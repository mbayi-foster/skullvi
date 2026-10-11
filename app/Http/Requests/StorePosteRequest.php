<?php

namespace App\Http\Requests;

use App\Models\Poste;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePosteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('create', Poste::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'competences_requises' => ['required', 'array', 'min:1'],
            'competences_requises.*' => ['required', 'string', 'max:100', 'distinct'],
            'langues_attendues' => ['nullable', 'array'],
            'langues_attendues.*' => ['required', 'string', 'max:100', 'distinct'],
            'experience_min' => ['required', 'numeric', 'min:0', 'max:99.9'],
            'experience_ideale' => ['required', 'numeric', 'min:0', 'max:99.9', 'gte:experience_min'],
            'niveau_etudes_min' => ['required', Rule::in(Poste::NIVEAUX_ETUDES)],
            'actif' => ['required', 'boolean'],
        ];
    }

    /**
     * Supprime les lignes vides des listes répétables avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'competences_requises' => $this->normalizeList('competences_requises'),
            'langues_attendues' => $this->normalizeList('langues_attendues'),
        ]);
    }

    /**
     * @return list<string>
     */
    protected function normalizeList(string $key): array
    {
        $values = (array) $this->input($key, []);

        $cleaned = array_map(
            fn ($value) => is_string($value) ? trim($value) : $value,
            $values,
        );

        return array_values(array_filter(
            $cleaned,
            fn ($value) => is_string($value) && $value !== '',
        ));
    }
}
