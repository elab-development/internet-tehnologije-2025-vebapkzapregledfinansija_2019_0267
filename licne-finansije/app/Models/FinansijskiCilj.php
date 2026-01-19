<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinansijskiCilj extends Model
{
    use HasFactory;

    protected $table = 'finansijski_ciljevi';

    protected $fillable = [
        'idKorisnik',
        'naziv',
        'ciljni_iznos',
        'trenutni_iznos',
        'rok',
    ];

    protected $casts = [
        'rok'=>'date',
        'ciljni_iznos'=>'decimal:2',
        'trenutni_iznos'=>'decimal:2',
    ];

    public function korisnik()
    {
        return $this->belongsTo(User::class,'idKorisnik');
    }
}
