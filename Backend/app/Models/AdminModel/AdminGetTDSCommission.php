<?php

namespace App\Models\AdminModel;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminGetTDSCommission extends Model
{
    use HasFactory;

    protected $table = 'admin_get_tds_comm';

    protected $fillable = [
        'userId',
        'amount',
        'commission',
        'status'
    ];

    public function user()
{
    return $this->belongsTo(\App\Models\UserModel\User::class, 'userId', 'id');
}
}
