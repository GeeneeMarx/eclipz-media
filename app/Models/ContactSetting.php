<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class ContactSetting extends Model
{
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
}