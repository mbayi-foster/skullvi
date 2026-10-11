<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateAssessmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('view', $this->route('poste')) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'qualifie' => ['nullable', 'in:0,1'],
            'score' => ['nullable', 'integer', 'between:0,100'],
            'motif_rejet' => ['nullable', 'required_if:qualifie,0', 'string', 'max:1000'],
        ];
    }
}
