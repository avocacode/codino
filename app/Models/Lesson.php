<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['module_id', 'title', 'description', 'content_type', 'video_url', 'ebook_path', 'order'])]
class Lesson extends Model
{
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function sources(): HasMany
    {
        return $this->hasMany(LessonSource::class);
    }

    public function getEmbedUrl()
    {
        $url = $this->video_url;
        if (!$url) return '';

        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        $videoId = null;

        if (str_contains($url, 'youtu.be/')) {
            $parts = explode('youtu.be/', $url);
            $videoId = explode('?', $parts[1] ?? '')[0] ?? '';
        } elseif (str_contains($url, 'v=')) {
            parse_str(parse_url($url, PHP_URL_QUERY) ?? '', $query);
            $videoId = $query['v'] ?? null;
        }

        if ($videoId) {
            return "https://www.youtube.com/embed/" . $videoId;
        }

        return $url;
    }

    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class);
    }
}
