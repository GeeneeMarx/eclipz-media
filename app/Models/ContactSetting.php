<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class ContactSetting extends Model
{
    public const SOCIAL_FIELDS = ['instagram', 'facebook', 'linkedin', 'tiktok'];

    protected $fillable = [
        'phone', 'whatsapp', 'email', 'inquiry_notification_email', 'address',
        'instagram', 'facebook', 'linkedin', 'tiktok',
    ];

    /**
     * The single settings row (id = 1). The id is not mass-assignable, so it
     * is created with forceCreate; firstOrCreate(['id' => 1]) would silently
     * drop the id and insert a new row on every call whenever row 1 is missing.
     */
    public static function current(): self
    {
        $settings = static::find(1);

        if ($settings) {
            return $settings;
        }

        try {
            return static::forceCreate(['id' => 1]);
        } catch (UniqueConstraintViolationException) {
            // Another request created it a moment ago.
            return static::findOrFail(1);
        }
    }

    public function whatsappDigits(): string
    {
        return preg_replace('/\D+/', '', (string) $this->whatsapp);
    }

    /**
     * wa.me link with an optional prefilled message, or null when no usable
     * WhatsApp number is configured. wa.me needs the international format
     * (country code first, no leading 0), so a number starting with 0 is
     * treated as not configured instead of producing a broken link.
     */
    public function whatsappUrl(?string $message = null): ?string
    {
        $digits = $this->whatsappDigits();

        if ($digits === '' || str_starts_with($digits, '0')) {
            return null;
        }

        $url = "https://wa.me/{$digits}";

        if (filled($message)) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    /**
     * A safe link for one of the social profile fields, or null when it is empty or not a
     * usable web address. A missing scheme is added (instagram.com/eclipz becomes
     * https://instagram.com/eclipz). The result must be http or https, must have a real
     * domain name (so a bare handle such as "@eclipz" is rejected), and must not carry
     * embedded credentials (https://user@host). An admin-entered value can therefore never
     * become a script link or a misleading one.
     */
    public function socialUrl(string $field): ?string
    {
        if (! in_array($field, self::SOCIAL_FIELDS, true)) {
            return null;
        }

        $value = trim((string) $this->{$field});

        if ($value === '') {
            return null;
        }

        if (! preg_match('#^[a-z][a-z0-9+.-]*://#i', $value)) {
            $value = 'https://'.ltrim($value, '/');
        }

        $parts = parse_url($value);

        if ($parts === false) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        if (array_key_exists('user', $parts) || array_key_exists('pass', $parts)) {
            return null;
        }

        if (! preg_match('/^(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z]{2,}$/', $host)) {
            return null;
        }

        if (filter_var($value, FILTER_VALIDATE_URL) === false) {
            return null;
        }

        return $value;
    }
}