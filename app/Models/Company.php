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
    'custom_links',
    'language',
    'enable_multi_review_prompt',
    'notification_email',
    'industry',
    'keywords',
    'default_platform',
    'enable_gamification',
    'gamification_mode',
    'gamification_interval',
    'gamification_reward',
    'gamification_image_url',
    'custom_code',
])]
class Company extends Model
{
    protected $casts = [
        'custom_links' => 'array',
        'enable_multi_review_prompt' => 'boolean',
        'enable_gamification' => 'boolean',
        'gamification_interval' => 'integer',
    ];

    public function getLogoUrlAttribute($value): ?string
    {
        if (! $value) {
            return null;
        }
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }
        if (str_starts_with($value, '/storage/')) {
            return $value;
        }
        if (str_starts_with($value, 'storage/')) {
            return '/' . $value;
        }
        return '/storage/' . ltrim($value, '/');
    }

    /** Returns array of active review platform links for multi-destination selection if enabled. */
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

        // Only include secondary destinations if admin explicitly enabled the multi-review toggle switch!
        if ($this->enable_multi_review_prompt) {
            if (is_array($this->custom_links)) {
                foreach ($this->custom_links as $link) {
                    if (! empty($link['name']) && ! empty($link['url']) && filter_var($link['url'], FILTER_VALIDATE_URL)) {
                        $destinations[] = [
                            'key' => 'custom_'.md5($link['url']),
                            'name' => $link['name'],
                            'icon' => '🔗',
                            'bg' => 'linear-gradient(135deg, #6366f1, #4f46e5)',
                            'url' => $link['url'],
                        ];
                    }
                }
            }
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
