<?php

class Database
{
    public static function connect(string $path): PDO
    {
        $pdo = new PDO('sqlite:' . /$path);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODDE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}