<?php

namespace App\Models\Concerns;

use App\Models\Company;
use Illuminate\Database\Eloquent\Relations\HasMany;

// A "trait" in PHP is a chunk of reusable code you can mix into a class
// without inheritance -- like a plug-in module. We use this instead of
// editing riyaloerp's existing User model directly, since Claude doesn't
// have access to that file. To wire it up:
//
//   1. Open app/Models/User.php in riyaloerp
//   2. Add: use App\Models\Concerns\HasReviewCompanies;
//   3. Add "use HasReviewCompanies;" inside the class body
//
// That's it -- $user->companies and $user->currentCompany() will then work.
trait HasReviewCompanies
{
    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    // Mirrors the Python get_current_company() function: look at what's
    // stored in the session, fall back to the user's first company, and
    // remember the choice in the session for next time.
    public function currentCompany(): ?Company
    {
        $companyId = session('company_id');

        if ($companyId) {
            $company = $this->companies()->find($companyId);
            if ($company) {
                return $company;
            }
        }

        $company = $this->companies()->orderBy('id')->first();

        if ($company) {
            session(['company_id' => $company->id]);
        }

        return $company;
    }
}
