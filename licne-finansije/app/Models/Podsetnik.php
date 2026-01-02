<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Podsetnik extends Model
{
    protected $table = 'podsetnici';

    protected $fillable = [
        'idKorisnik',
        'opis',
        'datum',
        'status',
    ];

    protected $casts = [
        'datum'=>'datetime',
        'status'=>'boolean',
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class,'idKorisnik');
    }
}
