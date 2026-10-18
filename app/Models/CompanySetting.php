<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class CompanySetting extends Model
{
    protected $fillable = [
        'company_name', 'tagline', 'short_description', 'story', 'what_we_do', 'differentiator', 'client_type_text',
        'mission', 'vision',
        'founder_name', 'founder_title', 'founder_bio', 'founder_specializations', 'founder_quote',
        'logo', 'favicon',
    ];

    /** The single settings row (id = 1). See ContactSetting::current() for why forceCreate is used. */
    public static function current(): self
    {
        $settings = static::find(1);

        if ($settings) {
            return $settings;
        }

        try {
            return static::forceCreate(['id' => 1]);
        } catch (UniqueConstraintViolationException) {
            return static::findOrFail(1);
        }
    }

    /** Comma-separated specializations as a clean array for Blade. */
    public function specializationsList(): array
    {
        return $this->founder_specializations
            ? array_map('trim', explode(',', $this->founder_specializations))
            : [];
    }
}