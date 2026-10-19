<?php

return [
    'db_path'     => __DIR__ . '/database/links.sqlite',
    'base_url'    => getenv('APP_URL') ?: 'http://localhost:8000',
    'code_length' => 6,
];