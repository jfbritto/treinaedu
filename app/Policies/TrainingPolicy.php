<?php

namespace App\Policies;

use App\Models\Training;
use App\Models\User;

class TrainingPolicy
{
    public function update(User $user, Training $training): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $training->created_by === $user->id;
    }

    public function delete(User $user, Training $training): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isInstructor() && $training->created_by === $user->id;
    }
}
