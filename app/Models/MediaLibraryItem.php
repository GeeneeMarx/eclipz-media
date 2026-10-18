<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLibraryItem extends Model
{
    protected $table = 'media_library_items';

    protected $fillable = ['path', 'label', 'mime_type', 'size_bytes', 'uploaded_by'];

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
