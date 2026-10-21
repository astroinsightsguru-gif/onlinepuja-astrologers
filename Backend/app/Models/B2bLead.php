<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class B2bLead extends Model
{
    use HasFactory;

    protected $table = 'b2b_leads';

    protected $fillable = [
        'business_name',
        'contact_person',
        'phone',
        'email',
        'category',
        'city',
        'state',
        'address',
        'pincode',
        'google_rating',
        'google_reviews_count',
        'google_place_id',
        'website',
        'source',
        'status',
        'partner_code',
        'commission_pct',
        'total_bookings',
        'total_revenue',
        'last_contacted_at',
        'notes',
    ];

    protected $casts = [
        'google_rating'       => 'float',
        'google_reviews_count'=> 'integer',
        'commission_pct'      => 'float',
        'total_bookings'      => 'integer',
        'total_revenue'       => 'float',
        'last_contacted_at'   => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (B2bLead $lead) {
            if (empty($lead->partner_code)) {
                $cityCode = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $lead->city ?? 'IND'), 0, 3));
                $lead->partner_code = 'B2B-' . ($cityCode ?: 'OP') . '-' . strtoupper(Str::random(5));
            }
        });
    }

    /**
     * Normalized 10-digit or 91-prefixed WhatsApp mobile number.
     */
    public function getCleanPhoneAttribute(): string
    {
        $clean = preg_replace('/[^0-9]/', '', (string)$this->phone);
        if (strlen($clean) === 10) {
            $clean = '91' . $clean;
        }
        return $clean;
    }

    /**
     * Unique trackable booking URL for this partner.
     */
    public function getPartnerBookingUrl(string $slug = 'dev-deepawali-maha-deepdaan-kashi-vishwanath-sankalp'): string
    {
        return "https://onlinepuja.live/pujas/{$slug}?partner=" . urlencode($this->partner_code);
    }
}
