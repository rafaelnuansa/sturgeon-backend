<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Str;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    /**
     * guard_name
     *
     * @var string
     */
    protected $guard_name = 'api';

    // public $incrementing = false;
    // protected $keyType = 'string';


    public function getIncrementing()
    {
        return false;
    }

    /**
     * Get the auto-incrementing key type.
     *
     * @return string
     */
    public function getKeyType()
    {
        return 'string';
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * getPermissionArray
     *
     * @return void
     */
    // public function getPermissionArray()
    // {
    //     return $this->getAllPermissions()->mapWithKeys(function($pr){
    //         return [$pr['name'] => true];
    //     });

    // }

    /**
     * getJWTIdentifier
     *
     * @return void
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * getJWTCustomClaims
     *
     * @return void
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function booted()
    {
        parent::booted();

        static::creating(function ($user) {
            $user->id = Str::uuid()->toString(); // Membuat UUID baru sebagai string
        });
    }

    // relations
    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function scientific_works()
    {
        return $this->hasMany(ScientificWork::class);
    }

    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    // }
}
