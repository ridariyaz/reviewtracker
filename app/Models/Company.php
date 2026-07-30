<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A business location/brand owned by an admin user.
 *
 * google_review_url: destination for customers who pick "Good".
 * logo_url / primary_color / secondary_color: customer-facing branding.
 */
#[Fillable([
    'user_id',
    'name',
    'logo_url',
    'primary_color',
    'secondary_color',
    'google_review_url',
    'tripadvisor_review_url',
    'yelp_review_url',
    'trustpilot_review_url',
])]
class Company extends Model
{
    /** Returns array of all active review platform links for multi-destination selection. */
    public function configuredReviewDestinations(): array
    {
        $destinations = [];

        if ($this->hasValidGoogleReviewUrl()) {
            $destinations[] = [
                'key' => 'google',
                'name' => 'Google Reviews',
                'icon' => '🌐',
                'bg' => 'linear-gradient(135deg, #4285f4, #34a853)',
                'url' => $this->google_review_url,
            ];
        }

        if (! empty($this->tripadvisor_review_url) && filter_var($this->tripadvisor_review_url, FILTER_VALIDATE_URL)) {
            $destinations[] = [
                'key' => 'tripadvisor',
                'name' => 'TripAdvisor',
                'icon' => '🦉',
                'bg' => 'linear-gradient(135deg, #00af87, #008767)',
                'url' => $this->tripadvisor_review_url,
            ];
        }

        if (! empty($this->yelp_review_url) && filter_var($this->yelp_review_url, FILTER_VALIDATE_URL)) {
            $destinations[] = [
                'key' => 'yelp',
                'name' => 'Yelp',
                'icon' => '⭐',
                'bg' => 'linear-gradient(135deg, #d32323, #af1616)',
                'url' => $this->yelp_review_url,
            ];
        }

        if (! empty($this->trustpilot_review_url) && filter_var($this->trustpilot_review_url, FILTER_VALIDATE_URL)) {
            $destinations[] = [
                'key' => 'trustpilot',
                'name' => 'Trustpilot',
                'icon' => '★',
                'bg' => 'linear-gradient(135deg, #00b67a, #008f60)',
                'url' => $this->trustpilot_review_url,
            ];
        }

        return $destinations;
    }
    /**
     * True when a usable Google review / Maps URL is configured.
     * Bare google.com is treated as incomplete (that is only a fallback redirect).
     */
    public function hasValidGoogleReviewUrl(): bool
    {
        $url = trim((string) $this->google_review_url);
        if ($url === '' || filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parts = parse_url($url);
        if (! $parts || ! in_array($parts['scheme'] ?? '', ['http', 'https'], true)) {
            return false;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '/';

        if (in_array($host, ['google.com', 'www.google.com'], true) && ($path === '/' || $path === '')) {
            return false;
        }

        return true;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    public function feedback(): HasMany
    {
        return $this->hasMany(Feedback::class);
    }

    public function scanLogs(): HasMany
    {
        return $this->hasMany(ScanLog::class);
    }
}
