<?php

// every query on the links table lives here.
// all of them use prepared statements (the ? placeholders) to avoid sql injection
class LinkRepository
{
    public function __construct(private PDO $db)
    {
    }

    // save a new link, then return the full row including id and created_at
    public function create(string $url, string $code): array
    {
        $stmt = $this->db->prepare('INSERT INTO links (code, url) VALUES (?, ?)');
        $stmt->execute([$code, $url]);

        return $this->findByCode($code);
    }