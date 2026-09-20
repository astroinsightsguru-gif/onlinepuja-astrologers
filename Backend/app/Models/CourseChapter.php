<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseChapter extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'chapter_name',
        'chapter_description',
        'chapter_images',
        'youtube_link',
        'chapter_document',
        'isActive',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'chapter_images' => 'array',

    ];

    public function course()
    {
        return $this->hasOne(Course::class,'id',localKey: 'course_id');
    }
}
