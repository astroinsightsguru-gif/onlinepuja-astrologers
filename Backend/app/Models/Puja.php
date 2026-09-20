<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Puja extends Model
{
    use HasFactory;

    protected $table = 'pujas';

    protected $fillable = [
        'category_id',
        'sub_category_id',
        'puja_title',
        'puja_subtitle',
        'puja_place',
        'long_description',
        'puja_benefits',
        'puja_images',
        'package_id',
        'puja_start_datetime',
        'puja_end_datetime',
        'slug',
        'astrologerId',
        'created_by',
        'puja_price',
        'isAdminApproved',
        'puja_duration',
        'isPujaEnded',
        'actual_puja_endtime'
    ];

    protected $casts = [
        'puja_benefits' => 'array',
        'puja_images' => 'array',
        'package_id' => 'array',
    ];

    public function package()
    {
        return Pujapackage::whereIn('id', $this->package_id)->get();
    }

    public function astrologer()
    {
        return Astrologer::where('id', $this->astrologerId)->first();
    }

    public function category()
    {
        return $this->hasOne(PujaCategory::class, 'id', localKey: 'category_id');
    }


    public function singlepackage($puja_package_id)
    {
        return Pujapackage::where('id', $puja_package_id)->first();
    }


    public function astrologerRelation()
    {
        return $this->belongsTo(Astrologer::class, 'astrologerId');
    }
    
    public function getPujaImagesAttribute($value): array
    {
        // Decode if stored as JSON string
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
    
        // Ensure it's always an array
        if (empty($value) || !is_array($value)) {
            return [];
        }
    
        return array_map(function ($imagePath) {
            if (Str::startsWith($imagePath, ['http://', 'https://'])) {
                return $imagePath;
            }
    
            return asset($imagePath);
        }, $value);
    }
}
