<?php

namespace App\Services;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Resolves which company an admin is currently working in.
 * Active company id is stored in the session as company_id.
 */
class CompanyContext
{
    public function companiesFor(User $user): Collection
    {
        return $user->companies()->orderBy('id')->get();
    }

    public function currentFor(User $user): ?Company
    {
        $companyId = session('company_id');

        if ($companyId) {
            $company = $user->companies()->whereKey($companyId)->first();
            if ($company) {
                return $company;
            }
        }

        $company = $user->companies()->orderBy('id')->first();
        if ($company) {
            session(['company_id' => $company->id]);
        }

        return $company;
    }

    /** Ensure the user has a company (create a default one if needed). */
    public function ensureDefaultCompany(User $user): Company
    {
        $company = $this->currentFor($user);
        if ($company) {
            return $company;
        }

        $company = $user->companies()->create([
            'name' => "{$user->username}'s Company",
        ]);
        session(['company_id' => $company->id]);

        return $company;
    }
}
