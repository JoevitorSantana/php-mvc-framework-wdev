<?php

use App\Utils\View;
use WilliamCosta\DatabaseManager\Database;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../app/utils/Config.php';

View::init([
    'URL' => URL
]);

Database::config(
    DB_HOST,
    DB_NAME,
    DB_USER,
    DB_PASS,
    DB_PORT
);