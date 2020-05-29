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
        'password', 'created_at', 'updated_at', 'deleted_at'
    ];

    public function refreshToken()
    {
        return $this->hasOne(UserRefreshToken::class, 'user_id');
    }
}
