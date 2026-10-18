<?php

namespace App\Models;

use App\Enums\ProjectType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Project extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'industry_id',
        'client_partner_id',
        'project_type',
        'description',
        'short_description',
        'location',
        'cover_image',
        'video_url',
        'project_url',
        'year',
        'is_featured',
        'is_published',
        'published_at',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'project_type' => ProjectType::class,
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Project $project) {
            if ($project->cover_image) {
                Storage::disk('public')->delete($project->cover_image);
            }

            foreach ($project->images as $image) {
                Storage::disk('public')->delete($image->path);
            }
        });
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    public function client()
    {
        return $this->belongsTo(ClientPartner::class, 'client_partner_id');
    }

    public function services()
    {
        return $this->belongsToMany(Service::class);
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    public function caseStudyDetail()
    {
        return $this->hasOne(CaseStudyDetail::class);
    }

    public function metrics()
    {
        return $this->hasMany(ProjectMetric::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * The project's own Eclipz portfolio page URL (/work/{slug}), generated
     * from the named route and current slug - never stored, never
     * hand-concatenated with APP_URL. Not to be confused with the
     * `project_url` column, which is a Digital Experience project's
     * external live site link.
     */
    public function publicUrl(): string
    {
        return route('work.show', $this->slug);
    }

    public function videoEmbedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]+)/', $this->video_url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }

        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return null;
    }

    public function isDirectVideoFile(): bool
    {
        return (bool) $this->video_url
            && ! $this->videoEmbedUrl()
            && (bool) preg_match('/\.(mp4|webm|ogg)(\?.*)?$/i', $this->video_url);
    }
}