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

      // look up a link by its short code, null if there isn't one
    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM links WHERE code = ?');
        $stmt->execute([$code]);

        return $stmt->fetch() ?: null;
    }

    // used to check if a url has already been shortened
    public function findByUrl(string $url): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM links WHERE url = ?');
        $stmt->execute([$url]);

        return $stmt->fetch() ?: null;
    }

    // newest links first, for the list on the frontend
    public function recent(int $limit = 20): array
    {
        $stmt = $this->db->prepare('SELECT * FROM links ORDER BY id DESC LIMIT ?');
        // LIMIT needs a real integer, so bind it as one
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // add one to the click count when someone visits a short link
    public function addClick(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE links SET clicks = clicks + 1 WHERE id = ?');
        $stmt->execute([$id]);
    }
}