<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRefreshToken extends Model
{
    use SoftDeletes;

    protected $table = 'user_refresh_token';

    protected $fillable = [
        'user_id', 'token', 'expired_at'
    ];

    protected $hidden = [
        'expired_at', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
