<?php

namespace App\Policies;

use App\Models\Poste;
use App\Models\User;

class PostePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Poste $poste): bool
    {
        return $this->owns($user, $poste);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Poste $poste): bool
    {
        return $this->owns($user, $poste);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Poste $poste): bool
    {
        return $this->owns($user, $poste);
    }

    /**
     * Un poste n'appartient qu'à son créateur ; un poste orphelin n'est modifiable par personne.
     */
    protected function owns(User $user, Poste $poste): bool
    {
        return $poste->user_id !== null && $user->id === $poste->user_id;
    }
}
