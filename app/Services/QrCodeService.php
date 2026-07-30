<?php

namespace App\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Support\Facades\Storage;

/**
 * Generates PNG QR codes for employee public review URLs.
 * Files are stored at storage/app/public/qrcodes/{employeeId}.png
 */
class QrCodeService
{
    public function generateForEmployee(int $employeeId, string $url): string
    {
        $builder = new Builder(
            writer: new PngWriter,
            data: $url,
            size: 300,
            margin: 10,
        );

        $result = $builder->build();
        $path = "qrcodes/{$employeeId}.png";
        Storage::disk('public')->put($path, $result->getString());

        return Storage::disk('public')->url($path);
    }
}
