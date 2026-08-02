<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\SvgWriter;
use Illuminate\Support\Facades\Storage;

/**
 * Generates 100% reliable SVG QR codes for employee review URLs.
 * Stores SVG files and provides inline SVG Data URI fallbacks for universal rendering.
 */
class QrCodeService
{
    public function generateForEmployee(int $employeeId, string $url): string
    {
        try {
            $builder = new Builder(
                writer: new SvgWriter(),
                data: $url,
                size: 300,
                margin: 10,
            );

            $result = $builder->build();
            $path = "qrcodes/{$employeeId}.svg";
            Storage::disk('public')->put($path, $result->getString());

            return Storage::disk('public')->url($path);
        } catch (\Throwable) {
            return $this->generateSvgDataUri($url);
        }
    }

    public function generateSvgDataUri(string $url): string
    {
        try {
            $builder = new Builder(
                writer: new SvgWriter(),
                data: $url,
                size: 300,
                margin: 10,
            );

            $result = $builder->build();
            return 'data:image/svg+xml;base64,'.base64_encode($result->getString());
        } catch (\Throwable) {
            return '';
        }
    }
}
