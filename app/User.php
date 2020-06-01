<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'username', 'password'
    ];

    protected $hidden = [
        'password'
    ];

    public function getRefreshTokenAttribute()
    {
        return UserRefreshToken::select('id', 'user_id', 'token', 'key', 'user_agent', 'expired_at')->where('user_id', $this->attributes['id'])->first();
    }
}
