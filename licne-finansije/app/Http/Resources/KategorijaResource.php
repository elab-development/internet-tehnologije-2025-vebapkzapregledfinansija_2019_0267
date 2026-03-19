<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KategorijaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'idKategorija' => $this->id,
            'idKorisnik' => $this->idKorisnik,
            'naziv' => $this->naziv,
            'opis' => $this->opis,
        ];
    }
}
