<?php

namespace App\Services;

use ColorThief\ColorThief;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Saves company logo uploads and extracts dominant brand color swatches.
 */
class LogoService
{
    /**
     * @return array{logo_url: ?string, primary_hex: ?string, secondary_hex: ?string, palette: array<string>}
     */
    public function saveAndExtractColors(?UploadedFile $file, int $companyId): array
    {
        if (! $file) {
            return [
                0 => null,
                1 => null,
                2 => null,
                'logo_url' => null,
                'primary_hex' => null,
                'secondary_hex' => null,
                'palette' => [],
            ];
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
        $filename = "company_{$companyId}.{$ext}";
        $path = $file->storeAs('logos', $filename, 'public');
        $fullPath = Storage::disk('public')->path($path);
        $logoUrl = '/storage/' . $path;

        $paletteHex = [];

        try {
            $thief = new ColorThief(quality: 3);
            $dominant = $thief->getColor($fullPath);
            $palette = $thief->getPalette($fullPath, 6);

            if ($palette) {
                foreach ($palette as $color) {
                    $hex = $this->rgbToHex($color->red(), $color->green(), $color->blue());
                    if (! in_array($hex, $paletteHex, true)) {
                        $paletteHex[] = $hex;
                    }
                }
            }

            $primary = $dominant ? $this->rgbToHex($dominant->red(), $dominant->green(), $dominant->blue()) : '#0d6efd';
            $secondary = (count($paletteHex) > 1) ? $paletteHex[1] : '#020617';

            return [
                0 => $logoUrl,
                1 => $primary,
                2 => $secondary,
                'logo_url' => $logoUrl,
                'primary_hex' => $primary,
                'secondary_hex' => $secondary,
                'palette' => $paletteHex,
            ];
        } catch (\Throwable) {
            return [
                0 => $logoUrl,
                1 => '#0d6efd',
                2 => '#020617',
                'logo_url' => $logoUrl,
                'primary_hex' => '#0d6efd',
                'secondary_hex' => '#020617',
                'palette' => ['#0d6efd', '#2563eb', '#1e40af', '#020617', '#0f172a', '#1e293b'],
            ];
        }
    }

    public function extractPaletteFromImagePath(string $fullPath): array
    {
        try {
            $thief = new ColorThief(quality: 3);
            $palette = $thief->getPalette($fullPath, 6);
            $hexList = [];
            if ($palette) {
                foreach ($palette as $color) {
                    $hexList[] = $this->rgbToHex($color->red(), $color->green(), $color->blue());
                }
            }
            return $hexList;
        } catch (\Throwable) {
            return ['#0d6efd', '#2563eb', '#1e40af', '#020617', '#0f172a', '#1e293b'];
        }
    }

    private function rgbToHex(int $r, int $g, int $b): string
    {
        return sprintf('#%02x%02x%02x', $r, $g, $b);
    }
}
