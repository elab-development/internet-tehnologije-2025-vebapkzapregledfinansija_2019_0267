<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategorija extends Model  
{
    use HasFactory;

    protected $table = 'kategorije';
    
    protected $fillable = [
        'idKategorija',
        'naziv',
        'opis'

    ];

    public function transakcije()
    {
        return $this->hasMany(Transakcija::class,'idKategorija');
    }
}
