<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Podsetnik extends Model
{
    use HasFactory;

    protected $table = 'podsetnici';

    protected $fillable = [
        'idKorisnik',
        'opis',
        'datum_vreme',
        'status', 
    ];

    protected $casts = [
        'datum_vreme'=>'datetime',
        'status'=>'boolean',
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class,'idKorisnik');
    }
}
