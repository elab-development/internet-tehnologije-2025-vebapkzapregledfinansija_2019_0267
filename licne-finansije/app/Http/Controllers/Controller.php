<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Veb aplikacija za pregled licnih finansija",
    description: "API za upravljanje ličnim finansijama (autentifikacija, transakcije, kategorije, budžeti, finansijski ciljevi, podsetnici, dokumenta i admin statistike)"
)]

#[OA\Server(url: "http://localhost")]
abstract class Controller
{
    //
}
