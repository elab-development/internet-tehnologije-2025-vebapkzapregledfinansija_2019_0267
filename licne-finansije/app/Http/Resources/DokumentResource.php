<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DokumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'idTransakcija' => $this->transakcija_id,
            'nazivFajla' => $this->naziv,
            'datumDodavanja' => $this->datum?->format('d.m.Y.'),
            'putanja' => $this->putanja,
            'tip' => $this->tip,
        ];
    }
}
