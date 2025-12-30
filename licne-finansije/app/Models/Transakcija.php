<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\TipTransakcije;

class Transakcija extends Model
{
    protected $fillable = [
        'idKorisnik',
        'idKategorija',
        'datumVreme',
        'tipTransakcije',
        'iznos',
        'opis',
    ];

    protected $casts = [
        'datumVreme'=>'datetime',
        'iznos'=>'decimal:2',
        'tipTransakcije'=>TipTransakcije::class,
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class,'idKorisnik');
    }

    public function kategorija()
    {
        return $this->belongsTo(Kategorija::class,'idKategorija');
    }

    public function dokumenti()
    {
        return $this->hasMany(Dokument::class,'idTransakcija');
    }
}
