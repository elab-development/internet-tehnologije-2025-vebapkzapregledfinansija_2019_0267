<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransakcijaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'idTransakcija' => $this->id,
            'idKorisnik' => $this->idKorisnik,
            'idKategorija' => $this->idKategorija,
            'kategorija' => $this->kategorija,
            'datum_vreme' => $this->datum_vreme ? $this->datum_vreme->format('Y-m-d H:i:s') : null,
            'tipTransakcije' => $this->tipTransakcije->value,
            'iznos' => $this->iznos,
            'valuta' => $this->valuta,
            'opis' => $this->opis,
        ];
    }
}
