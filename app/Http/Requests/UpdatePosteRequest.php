<?php

namespace App\Http\Requests;

class UpdatePosteRequest extends StorePosteRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('poste')) ?? false;
    }
}
