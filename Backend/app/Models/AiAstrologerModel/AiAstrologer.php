<?php

namespace App\Models\AiAstrologerModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Skill;
use App\Models\AstrologerCategory;
use Illuminate\Support\Str;

use DB;
use Illuminate\Support\Facades\Auth;

class AiAstrologer extends Model
{
    use HasFactory;

    protected $table = 'aiastrologers';

    protected $fillable = [
        'name',
        'about',
        'image',
        'astrologerCategoryId',
        'primary_skill',
        'all_skills',
        'chat_charge',
        'chat_charge_usd',
        'experience',
        'system_intruction',
        'type'
    ];

    public function primarySkills()
    {
        return Skill::whereIn('id', explode(',', $this->primary_skill))->get();
    }

    public function allSkills()
    {
        return Skill::whereIn('id', explode(',', $this->all_skills))->get();
    }

    public function astrologerCategories()
    {
        return AstrologerCategory::whereIn('id', explode(',', $this->astrologerCategoryId))->get();
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($AiAstrologer) {
            $slug = Str::slug($AiAstrologer->name, '.');
            $decimalPart = null;
            $parts = explode(' ', $AiAstrologer->name);
            if (count($parts) > 1 && is_numeric(end($parts))) {
                $decimalPart = end($parts);
                array_pop($parts); // Remove the decimal part from the array
                $AiAstrologer->name = implode(' ', $parts); // Rebuild the course name without the decimal part
            }
            // Ensure the slug is unique
            $originalSlug = $slug;
            $count = 1;
            while (static::where('slug', $slug)->where('id', '!=', $AiAstrologer->id)->exists()) {
                $slug = $originalSlug . '.' . $count++;
            }
            // If decimal part exists, append it to the slug
            if ($decimalPart !== null) {
                $slug .= '.' . $decimalPart;
            }
            // Set the unique slug
            $AiAstrologer->slug = $slug;
        });
    }


    public function getChatChargeAttribute($val)
    {
        $country = '';
        if(authcheck() || Auth::guard('api')->user()){
            $authCountryCode = isset(authcheck()['countryCode'])?authcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($authCountryCode) == '+91' ? 'india' : 'international';
        }
        elseif(astroauthcheck()){
            $astroCountryCode = isset(astroauthcheck()['countryCode'])?astroauthcheck()['countryCode']:Auth::guard('api')->user()->countryCode;
            $country = strtolower($astroCountryCode) == '+91' ? 'india' : 'international';
        }
        $amount = $country=='international' ? $this->chat_charge_usd:$val;
        if(systemflag('walletType') == 'Coin'){
           $amount = $country =='international' ? convertusdtocoin($this->chat_charge_usd):convertinrtocoin($val);
        }
        return $amount;
    }
}
