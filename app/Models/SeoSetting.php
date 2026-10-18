<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\UniqueConstraintViolationException;

class SeoSetting extends Model
{
    protected $fillable = ['default_meta_title', 'default_meta_description', 'default_og_image'];

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
}