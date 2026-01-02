<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinansijskiCilj extends Model
{
    protected $table = 'finansijskiCiljevi';

    protected $fillable = [
        'idKorisnik',
        'naziv',//DAL DA DODAMO OVAJ ATRIBUT
        'ciljaniIznos',
        'trenutniIznos',
        'rok',
    ];

    protected $casts = [
        'rok'=>'date',
        'ciljaniIznos'=>'decimal:2',
        'trenutniIznos'=>'decimal:2',

    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class,'idKorisnik');
    }
}
