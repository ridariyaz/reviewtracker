<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CompanyController extends Controller
{
    // GET /admin/companies
    public function index(Request $request): View
    {
        $user = $request->user();
        $companies = $user->companies()->orderBy('id')->get();
        $currentCompany = $user->currentCompany();

        return view('admin.companies', compact('companies', 'currentCompany'));
    }

    // POST /admin/companies
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo_file' => 'nullable|image|max:4096', // max size in kilobytes (4MB)
            'google_review_url' => 'nullable|url',
        ]);

        $company = Company::create([
            'user_id' => $request->user()->id,
            'name' => $data['name'],
            'google_review_url' => $data['google_review_url'] ?? null,
        ]);

        if ($request->hasFile('logo_file')) {
            $this->handleLogoUpload($request, $company);
        }

        session(['company_id' => $company->id]);

        return redirect()->route('admin.companies');
    }

    // POST /admin/companies/{company}
    public function update(Request $request, Company $company): RedirectResponse
    {
        $this->authorizeCompany($request, $company);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'logo_file' => 'nullable|image|max:4096',
            'google_review_url' => 'nullable|url',
        ]);

        $company->update([
            'name' => $data['name'],
            'google_review_url' => $data['google_review_url'] ?? null,
        ]);

        if ($request->hasFile('logo_file')) {
            $this->handleLogoUpload($request, $company);
        }

        return redirect()->route('admin.companies');
    }

    // POST /admin/companies/switch
    public function switch(Request $request): RedirectResponse
    {
        $data = $request->validate(['company_id' => 'required|integer']);
        $company = $request->user()->companies()->find($data['company_id']);

        if ($company) {
            session(['company_id' => $company->id]);
        }

        return redirect()->back();
    }

    // Makes sure a company belongs to the logged-in user before letting
    // them edit it -- a guard against someone editing another user's data
    // just by changing the ID in a URL.
    private function authorizeCompany(Request $request, Company $company): void
    {
        abort_if($company->user_id !== $request->user()->id, 403);
    }

    // Saves the uploaded logo and extracts a primary/secondary color from it.
    // PHP's GD image library doesn't have a one-line "get dominant color"
    // function like Python's colorthief package, so we do it manually:
    // shrink the image down to a tiny thumbnail (this blurs out noise/detail)
    // and read the pixel colors that remain.
    private function handleLogoUpload(Request $request, Company $company): void
    {
        $file = $request->file('logo_file');
        $filename = "company_{$company->id}." . $file->getClientOriginalExtension();

        // Storage::disk('public') writes to storage/app/public, which is
        // symlinked to public/storage via `php artisan storage:link`.
        Storage::disk('public')->putFileAs('logos', $file, $filename);
        $fullPath = Storage::disk('public')->path("logos/{$filename}");

        [$primary, $secondary] = $this->extractColors($fullPath);

        $company->update([
            'logo_url' => "/storage/logos/{$filename}",
            'primary_color' => $primary ?? $company->primary_color,
            'secondary_color' => $secondary ?? $company->secondary_color,
        ]);
    }

    /**
     * @return array{0: ?string, 1: ?string} [primaryHex, secondaryHex]
     */
    private function extractColors(string $path): array
    {
        if (!extension_loaded('gd')) {
            return [null, null];
        }

        try {
            $info = getimagesize($path);
            $mime = $info['mime'] ?? null;

            $image = match ($mime) {
                'image/png' => imagecreatefrompng($path),
                'image/jpeg' => imagecreatefromjpeg($path),
                'image/gif' => imagecreatefromgif($path),
                default => null,
            };

            if (!$image) {
                return [null, null];
            }

            // Resize to an 8x8 thumbnail -- shrinking averages out detail
            // and leaves us with a handful of representative colors, which
            // is a cheap stand-in for colorthief's clustering algorithm.
            $thumb = imagescale($image, 8, 8);
            $colorCounts = [];

            for ($x = 0; $x < 8; $x++) {
                for ($y = 0; $y < 8; $y++) {
                    $rgb = imagecolorat($thumb, $x, $y);
                    $r = ($rgb >> 16) & 0xFF;
                    $g = ($rgb >> 8) & 0xFF;
                    $b = $rgb & 0xFF;
                    $hex = sprintf('#%02x%02x%02x', $r, $g, $b);
                    $colorCounts[$hex] = ($colorCounts[$hex] ?? 0) + 1;
                }
            }

            arsort($colorCounts); // sort by frequency, most common first
            $sorted = array_keys($colorCounts);

            $primary = $sorted[0] ?? null;
            $secondary = $sorted[1] ?? $primary;

            return [$primary, $secondary];
        } catch (\Throwable $e) {
            return [null, null];
        }
    }
}
