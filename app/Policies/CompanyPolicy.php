<?php

namespace App\Policies;

use App\Models\Companies;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CompanyPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {

    }

    public function view(User $user, Companies $company): bool
    {
    }

    public function create(User $user): bool
    {
    }

    public function update(User $user, Companies $company): bool
    {
    }

    public function delete(User $user, Companies $company): bool
    {
    }

    public function restore(User $user, Companies $company): bool
    {
    }

    public function forceDelete(User $user, Companies $company): bool
    {
    }
}
