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
            'idKorisnik' => $this->idKorisnik,
            'idKategorija' => $this->idKategorija,
            'kategorija' => $this->kategorija,
            'datumVreme' => $this->datum_vreme ? $this->datum_vreme->format('d.m.Y. H:i:s') : null,
            'tipTransakcije' => $this->tipTransakcije,
            'iznos' => $this->iznos,
            'valuta' => $this->valuta,
        ];
    }
}
