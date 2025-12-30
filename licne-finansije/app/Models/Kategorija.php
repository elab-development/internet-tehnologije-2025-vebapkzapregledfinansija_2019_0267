<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategorija extends Model
{
    protected $fillable = [
        'naziv',
        // 'opis', //RAZMISLITI DAL NAM JE MOZDA I OVO POTREBNO POLJE

    ];

    public function transakcije()
    {
        return $this->hasMany(Transakcija::class,'idTransakcija');
    }
}
