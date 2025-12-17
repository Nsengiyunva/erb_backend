<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Contracts\Auth\MustVerifyEmail;


// use Tymon\JWTAuth\Contracts\JWTSubject;

class NECUser extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 'nec_users';
    protected $fillable = [ 
        'email_address', 
        'password',
        'role',
        'passcode'
    ];
    // protected $hidden = [ 'remember_token'];
    // protected $casts = [ 'email_verified_at' => 'datetime'];

    public function getJWTIdentifier(){
        return $this->getKey();
    }
    public function getJWTCustomClaims(){
        return [];
    }
}