<?php

return [
    /*
    |--------------------------------------------------------------------------
    | URL de l'application
    |--------------------------------------------------------------------------
    |
    | Cette configuration permet de centraliser les URLs utilisées par le backend
    | et le frontend afin d'éviter de modifier chaque template ou fichier.
    |
    */

    'app' => env('APP_URL', 'http://localhost'),
    'front_end' => env('APP_URL_FRONT_END', env('APP_URL', 'http://localhost')),
];
