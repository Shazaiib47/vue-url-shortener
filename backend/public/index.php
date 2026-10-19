<?php

// every request comes through this file

// load settings and classes
$config = require __DIR__ . '/../config.php';
require __DIR__ . '/../src/Database.php';
require __DIR__ . '/../src/LinkRepository.php';
require __DIR__ . '/../src/CodeGenerator.php';

$links = new LinkRepository(Database::connect($config['db_path']));

// work out what was requested, e.g. POST + /api/links
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// send a json response with a status code and stop
function respond(mixed $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
