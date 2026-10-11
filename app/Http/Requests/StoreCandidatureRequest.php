<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesListInput;
use App\Models\Poste;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreCandidatureRequest extends FormRequest
{
    use NormalizesListInput;

    /**
     * Une candidature est ouverte à tout visiteur ; l'unicité par offre est garantie par les règles.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('candidates', 'email')->where('poste_id', $this->poste()->id),
            ],
            'telephone' => ['nullable', 'string', 'max:30'],
            'annees_experience' => ['required', 'numeric', 'min:0', 'max:99.9'],
            'niveau_etudes' => ['required', Rule::in(Poste::NIVEAUX_ETUDES)],
            'competences' => ['required', 'array', 'min:1'],
            'competences.*' => ['required', 'string', 'max:100', 'distinct'],
            'langues' => ['nullable', 'array'],
            'langues.*' => ['required', 'string', 'max:100', 'distinct'],
            'cv' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'extensions:pdf,doc,docx', 'max:5120'],
            'portfolio_url' => ['nullable', 'url', 'max:255'],
        ];
    }

    /**
     * Normalise l'email (unicité par offre) et les listes répétables avant validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => Str::lower(trim((string) $this->input('email'))),
            'competences' => $this->normalizeList('competences'),
            'langues' => $this->normalizeList('langues'),
        ]);
    }

    /**
     * Offre à laquelle la candidature se rapporte.
     */
    protected function poste(): Poste
    {
        return $this->route('poste');
    }
}
