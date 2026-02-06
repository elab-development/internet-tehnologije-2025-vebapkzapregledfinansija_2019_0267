<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BudzetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return
        [
        'idBudzet' => $this->id,
        'idKorisnik' => $this->idKorisnik,
        'mesec' => $this->mesec,
        'godina' => $this->godina,
        'limit' => $this->limit,
        'potroseno' => $this->potroseno
        ];
    }
}
