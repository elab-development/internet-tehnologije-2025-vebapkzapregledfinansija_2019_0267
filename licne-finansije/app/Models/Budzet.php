<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budzet extends Model
{
    protected $fillable = [
        'idKorisnik',
        'mesec',
        'godina',
        'limit',
        'potroseno',
    ];

    protected $casts = [
        'limit'=>'decimal:2',
        'potroseno'=>'decimal:2',
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class,'idKorisnik');
    }
}
