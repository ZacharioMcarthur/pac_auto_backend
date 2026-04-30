<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'], // applique aux routes API

    'allowed_methods' => ['*'], // autorise toutes les méthodes : GET, POST, PUT, etc.

    'allowed_origins' => ['*'], // autorise tous les domaines (localhost, 127.0.0.1, React...)

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // autorise tous les en-têtes

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];
