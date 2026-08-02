<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

// All routes in here are admin-only. In routes/web.php we wrap this
// controller's routes in the 'auth' middleware group (Laravel's
// built-in login-check, equivalent to your @login_required decorator).
class EmployeeController extends Controller
{
    // GET /admin/employees
    public function index(Request $request): View
    {
        $company = $request->user()->currentCompany();

        $employees = Employee::where('company_id', $company->id)
            ->orderBy('name')
            ->get();

        return view('admin.employees', compact('employees', 'company'));
    }

    // POST /admin/employees
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $company = $request->user()->currentCompany();

        $employee = Employee::create([
            'company_id' => $company->id,
            'name' => $data['name'],
        ]);

        // Generate a QR code that points at the public review URL for this
        // employee, and save it as a PNG file, same as `qrcode.make(url)`
        // did in the Python version. `route()` builds the full URL for a
        // named route -- no hardcoded strings needed.
        $url = route('review.show', ['employee' => $employee->id]);
        $png = QrCode::format('png')->size(400)->generate($url);

        $path = storage_path('app/public/qrcodes');
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
        file_put_contents("{$path}/{$employee->id}.png", $png);

        return redirect()->route('admin.dashboard');
    }

    // POST /admin/employees/{employee}
    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate(['name' => 'required|string|max:255']);
        $employee->update(['name' => $data['name']]);

        return redirect()->route('admin.dashboard');
    }

    // POST /admin/employees/{employee}/credentials
    public function updateCredentials(Request $request, Employee $employee): RedirectResponse
    {
        $data = $request->validate([
            'employee_username' => 'required|string|max:255|unique:employees,employee_username,' . $employee->id,
            'employee_password' => 'required|string|min:4',
        ]);

        $employee->update([
            'employee_username' => $data['employee_username'],
            // Hash::make() is Laravel's password hashing helper --
            // the direct equivalent of Werkzeug's generate_password_hash().
            'employee_password' => Hash::make($data['employee_password']),
        ]);

        return redirect()->route('admin.dashboard');
    }

    // POST /admin/employees/{employee}/delete
    public function destroy(Employee $employee): RedirectResponse
    {
        // Deleting the employee also deletes their feedback rows automatically,
        // because the feedback migration set ->cascadeOnDelete() on employee_id.
        $employee->delete();

        return redirect()->route('admin.dashboard');
    }
}
