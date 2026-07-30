<?php

namespace App\Services;

use ColorThief\ColorThief;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Saves company logo uploads and optionally extracts dominant brand colors.
 *
 * @return array{0: ?string, 1: ?string, 2: ?string} [logo_url, primary_hex, secondary_hex]
 */
class LogoService
{
    public function saveAndExtractColors(?UploadedFile $file, int $companyId): array
    {
        if (! $file) {
            return [null, null, null];
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = "company_{$companyId}.{$ext}";
        $path = $file->storeAs('logos', $filename, 'public');
        $fullPath = Storage::disk('public')->path($path);
        $logoUrl = Storage::disk('public')->url($path);

        try {
            $thief = new ColorThief(quality: 3);
            $dominant = $thief->getColor($fullPath);
            $palette = $thief->getPalette($fullPath, 3);
            $secondary = ($palette->count() > 1) ? $palette[1] : $dominant;

            if (! $dominant) {
                return [$logoUrl, null, null];
            }

            return [
                $logoUrl,
                $this->rgbToHex($dominant->red(), $dominant->green(), $dominant->blue()),
                $secondary
                    ? $this->rgbToHex($secondary->red(), $secondary->green(), $secondary->blue())
                    : null,
            ];
        } catch (\Throwable) {
            return [$logoUrl, null, null];
        }
    }

    private function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
