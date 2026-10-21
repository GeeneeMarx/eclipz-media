<?php

namespace App\Models;

use App\Services\ActivityLogger;
use App\Support\SafeHtml;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'cover_image', 'cover_alt',
        'category', 'user_id', 'is_published', 'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        // Switching "Published" on without a date publishes now. A future date schedules it.
        static::saving(function (Post $post) {
            if ($post->is_published && $post->published_at === null) {
                $post->published_at = now();
            }
        });

        static::created(function (Post $post) {
            ActivityLogger::log('created', 'Post', $post->id, 'Created blog post "'.$post->title.'"');
        });

        static::updated(function (Post $post) {
            // A replaced cover image no longer needs its old file.
            if ($post->wasChanged('cover_image') && filled($post->getOriginal('cover_image'))) {
                Storage::disk('public')->delete($post->getOriginal('cover_image'));
            }

            $action = $post->wasChanged('is_published')
                ? ($post->is_published ? 'published' : 'unpublished')
                : 'updated';

            ActivityLogger::log($action, 'Post', $post->id, ucfirst($action).' blog post "'.$post->title.'"');
        });

        static::deleted(function (Post $post) {
            if (filled($post->cover_image)) {
                Storage::disk('public')->delete($post->cover_image);
            }

            ActivityLogger::log('deleted', 'Post', $post->id, 'Deleted blog post "'.$post->title.'"');
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Posts visitors may see: switched on, with a publish date that has arrived. */
    public function scopeLive(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function isLive(): bool
    {
        return $this->is_published
            && $this->published_at !== null
            && $this->published_at->lte(now());
    }

    public function statusLabel(): string
    {
        if (! $this->is_published) {
            return 'Draft';
        }

        return $this->isLive() ? 'Published' : 'Scheduled';
    }

    public function coverUrl(): ?string
    {
        return filled($this->cover_image) ? Storage::disk('public')->url($this->cover_image) : null;
    }

    /** The article HTML, cleaned for safe display. */
    public function safeBody(): string
    {
        return SafeHtml::clean($this->body);
    }

    public function plainText(): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $this->body))));
    }

    /** The excerpt, or the start of the article when there is no excerpt. */
    public function snippet(int $limit = 160): string
    {
        return filled($this->excerpt) ? $this->excerpt : Str::limit($this->plainText(), $limit);
    }

    public function readingMinutes(): int
    {
        $words = count(preg_split('/\s+/u', $this->plainText(), -1, PREG_SPLIT_NO_EMPTY) ?: []);

        return max(1, (int) ceil($words / 200));
    }
}