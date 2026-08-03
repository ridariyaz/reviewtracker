<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\CompanyContext;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

/**
 * Admin CRUD for employees under the active company.
 * Creating an employee also generates their review QR PNG.
 */
class EmployeeController extends Controller
{
    public function index(CompanyContext $companies)
    {
        $company = $companies->ensureDefaultCompany(Auth::user());
        $employees = $company->employees()->orderBy('name')->get();

        return view('employees.index', [
            'employees' => $employees,
            'brandName' => $company->name,
            'brandLogoUrl' => $company->logo_url,
            'companies' => $companies->companiesFor(Auth::user()),
            'currentCompany' => $company,
        ]);
    }

    public function store(Request $request, CompanyContext $companies, QrCodeService $qr)
    {
        try {
            $company = $companies->ensureDefaultCompany(Auth::user());

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'employee_username' => [
                    'nullable',
                    'string',
                    'max:255',
                    Rule::unique('employees', 'employee_username')->where(fn ($q) => $q->where('company_id', $company->id)),
                ],
                'employee_password' => ['nullable', 'string', 'min:6'],
            ], [
                'employee_username.unique' => 'An employee with this username already exists in your company. Please choose another username for this brand.',
                'employee_password.min' => 'Employee password must be at least 6 characters long.',
            ]);

            $employeeData = ['name' => $data['name']];
            if (! empty($data['employee_username'])) {
                $employeeData['employee_username'] = trim($data['employee_username']);
            }
            if (! empty($data['employee_password'])) {
                $employeeData['employee_password'] = $data['employee_password'];
            }

            $employee = $company->employees()->create($employeeData);

            // QR encodes the absolute URL to this employee's public review page.
            $qr->generateForEmployee(
                $employee->id,
                route('review.show', $employee->id)
            );

            return redirect()->back()->with('success', 'Employee created successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Unable to create employee: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Employee $employee, CompanyContext $companies)
    {
        try {
            $this->authorizeEmployee($employee, $companies);

            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
            ]);

            $employee->update(['name' => $data['name']]);

            return redirect()->route('admin')->with('success', 'Employee updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Unable to update employee: ' . $e->getMessage());
        }
    }

    /** Set credentials so the employee can use /employee/login. */
    public function updateCredentials(Request $request, Employee $employee, CompanyContext $companies)
    {
        try {
            $this->authorizeEmployee($employee, $companies);

            $data = $request->validate([
                'employee_username' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('employees', 'employee_username')
                        ->where(fn ($q) => $q->where('company_id', $employee->company_id))
                        ->ignore($employee->id),
                ],
                'employee_password' => ['required', 'string', 'min:6'],
            ], [
                'employee_username.unique' => 'An employee with this username already exists in your company. Please choose a unique username for this brand.',
                'employee_password.min' => 'Employee password must be at least 6 characters long.',
            ]);

            $employee->update([
                'employee_username' => trim($data['employee_username']),
                'employee_password' => $data['employee_password'],
            ]);

            return redirect()->route('admin')->with('success', 'Employee credentials updated successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', 'Unable to update employee credentials: ' . $e->getMessage());
        }
    }

    public function destroy(Employee $employee, CompanyContext $companies)
    {
        try {
            $this->authorizeEmployee($employee, $companies);
            $employee->feedback()->delete();
            $employee->delete();

            return redirect()->route('admin')->with('success', 'Employee deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Unable to delete employee: ' . $e->getMessage());
        }
    }

    private function authorizeEmployee(Employee $employee, CompanyContext $companies): void
    {
        $company = $companies->ensureDefaultCompany(Auth::user());
        abort_unless($employee->company_id === $company->id, 403);
    }
}
