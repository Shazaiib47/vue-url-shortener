<?php

// this creates the database connection that the rest of the app uses
class Database
{
    public static function connect(string $path): PDO
    {
        $pdo = new PDO('sqlite:' . $path);
        // throws an exception error when a query fails rather than providing nothing at all
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // returns rows as ['column_name' => 'value'] rather than [0 => 'value']
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}