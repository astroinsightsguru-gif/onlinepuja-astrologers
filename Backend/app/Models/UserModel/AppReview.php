<?php

namespace App\Models\UserModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class AppReview.
 * @OA\Schema(
 *     description="AppReview model",
 *     title="App Review model",
 *     required={"userId", "appId","review"},
 *     @OA\Xml(
 *         name="AppReview"
 *     )
 * )
 */

class AppReview extends Model
{
    use HasFactory;
    protected $table = 'app_reviews';
    protected $fillable = [
        'userId',
        'appId',
        'review',
        'createdBy',
        'modifiedBy'
    ];
}
