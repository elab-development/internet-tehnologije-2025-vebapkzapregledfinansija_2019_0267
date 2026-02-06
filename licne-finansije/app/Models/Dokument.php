<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dokument extends Model
{
    use HasFactory;

    protected $table = 'dokumenti';

    protected $fillable = [
        // 'idKorisnik',
        'idTransakcija',
        'nazivFajla',
        'datumDodavanja',
        'putanja', // DAL DA DODAMO I OVAJ ATRIBUT
        'tip', // DAL DA DODAMO I OVAJ ATRIBUT
    ];

    protected $casts = [
        'datum' => 'datetime',
    ];

    public function transakcija()
    {
        return $this->belongsTo(Transakcija::class, 'idTransakcija');
    }

    //  public function korisnik()
    // {
    //     return $this->belongsTo(User::class,'idKorisnik');
    // }
}
