<?php

namespace App\Http\Requests\Concerns;

trait NormalizesListInput
{
    /**
     * Nettoie une liste répétable : supprime les lignes vides et les espaces superflus.
     *
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
