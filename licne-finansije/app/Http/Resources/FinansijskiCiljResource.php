<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinansijskiCiljResource extends JsonResource
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
            'naziv' => $this->naziv,
            'ciljni_iznos' => $this->ciljni_iznos,
            'trenutni_iznos' => $this->trenutni_iznos,
            'rok' => $this->rok->format('d-m-Y'),
        ];
    }
}
