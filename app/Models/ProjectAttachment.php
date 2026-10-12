<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectAttachment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'disk',
        'file_name',
        'file_path',
        'file_size_kb',
        'mime_type',
        'uploaded_by',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return route('projects.attachments.download', $this);
    }

    public function getFormattedSizeAttribute(): string
    {
        if ($this->file_size_kb >= 1024) {
            return number_format($this->file_size_kb / 1024, 1).' MB';
        }

        return $this->file_size_kb.' KB';
    }
}
