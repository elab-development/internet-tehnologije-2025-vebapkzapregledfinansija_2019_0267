<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PodsetnikResource extends JsonResource
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
            'opis' => $this->opis,
            'datum_vreme' => $this->datum_vreme->format('d.m.Y. H:i:s'),
            'status' => $this->status, 
            'status_text' => $this->status ? 'Aktivan' : 'Neaktivan'
        ];
    }
}
