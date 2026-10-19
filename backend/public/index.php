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

// shape a database row into what the frontend needs, including the full short url
function present(array $link, string $baseUrl): array
{
    return [
        'code'       => $link['code'],
        'url'        => $link['url'],
        'short_url'  => rtrim($baseUrl, '/') . '/' . $link['code'],
        'clicks'     => (int) $link['clicks'],
        'created_at' => $link['created_at'],
    ];
}

// GET /api/links and list recent links
if ($path === '/api/links' && $method === 'GET') {
    $rows = $links->recent();
    respond(array_map(fn ($row) => present($row, $config['base_url']), $rows));
}

// POST /api/links - shorten a url
if ($path === '/api/links' && $method === 'POST') {
    // read the json body sent by the frontend
    $body = json_decode(file_get_contents('php://input'), true);
    $url = trim($body['url'] ?? '');

    // only accept a proper http/https urls
    if (!filter_var($url, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $url)) {
        respond(['error' => "That doesn't look like a valid http(s) URL."], 422);
    }

    // hand back the same code if it is already shortened, so the user can see the same short link again
    if ($existing = $links->findByUrl($url)) {
        respond(present($existing, $config['base_url']));
    }

    // 57^6 possible codes so a clash is unlikely, but check anyway
    do {
        $code = CodeGenerator::make($config['code_length']);
    } while ($links->findByCode($code));

    // 201 = created 
    respond(present($links->create($url, $code), $config['base_url']), 201);
}

// GET /abc123 - redirect to the original url
if ($method === 'GET' && preg_match('#^/([a-zA-Z0-9]{4,12})$#', $path, $matches)) {
    $link = $links->findByCode($matches[1]);

    if ($link) {
        $links->addClick($link['id']);
        header('Location: ' . $link['url'], true, 302);
        exit;
    }
}

// nothing matched, so send a 404 not found
respond(['error' => 'Not found'], 404);