<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokument extends Model
{
    protected $table = 'dokumenti';
    
    protected $fillable = [
        // 'idKorisnik',
        'idTransakcija',
        'nazivFajla',
        'datumDodavanja',
        'putanja',//DAL DA DODAMO I OVAJ ATRIBUT
        'tip',//DAL DA DODAMO I OVAJ ATRIBUT
    ];

    protected $casts = [
        'datumDodavanja'=>'datetime',
    ];


    public function transakcija()
    {
        return $this->belongsTo(Transakcija::class,'idTransakcija');
    }

    //  public function korisnik()
    // {
    //     return $this->belongsTo(User::class,'idKorisnik');
    // }
}
