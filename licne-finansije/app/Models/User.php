<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Enums\Uloga;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ime',
        'prezime',
        'email',
        'password',
        'uloga',
        'poeni',
        'nivo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'uloga' => Uloga::class,
        ];
    }

    public function podsetnici()
    {
        return $this->hasMany(Podsetnik::class,'idKorisnik');
    }

    public function budzeti()
    {
        return $this->hasMany(Budzet::class,'idKorisnik');
    }

    public function finansijskiCiljevi()
    {
        return $this->hasMany(FinansijskiCilj::class,'idKorisnik');
    }

    public function transakcije()
    {
        return $this->hasMany(Transakcija::class,'idKorisnik');
    }

    // public function dokumenti()
    // {
    //     return $this->hasMany(Dokument::class,'idKorisnik');
    // }


}
