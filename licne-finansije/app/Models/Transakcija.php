<?php

namespace App\Models;

use App\Enums\TipTransakcije;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transakcija extends Model
{
    use HasFactory;

    protected $table = 'transakcije';

    protected $fillable = [
        'idKorisnik',
        'idKategorija',
        'datumVreme',
        'tipTransakcije',
        'iznos',
        'opis',
        'valuta',
    ];

    protected $casts = [
        'datum_vreme' => 'datetime',
        'iznos' => 'decimal:2',
        'tipTransakcije' => TipTransakcije::class,
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class, 'idKorisnik');
    }

    public function kategorija()
    {
        return $this->belongsTo(Kategorija::class, 'idKategorija');
    }

    public function dokumenti()
    {
        return $this->hasMany(Dokument::class, 'idTransakcija');
    }
}
