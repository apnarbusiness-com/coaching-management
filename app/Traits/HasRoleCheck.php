<?php

namespace App\Traits;

trait HasRoleCheck
{
    public function isAdmin(): bool
    {
        return $this->roles()->whereRaw('LOWER(title) = ?', ['admin'])->exists();
    }

    public function isTeacher(): bool
    {
        return $this->roles()->whereRaw('LOWER(title) = ?', ['teacher'])->exists();
    }

    public function isStudent(): bool
    {
        return $this->roles()->whereRaw('LOWER(title) = ?', ['student'])->exists();
    }
}