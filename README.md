# Shorty

A small URL shortener. Paste a long URL, get a short code back, and the short link
redirects to the original while counting clicks.

This is built as a small standalone app, so I kept it deliberately small: no framework on the
backend, no state library on the frontend. Everything that happens is in the code
you can see.

## Stack

- **Backend:** PHP 8.1+, SQLite via PDO, run with PHP's built-in server
- **Frontend:** Vue 3 (Composition API) + Vite

## Project structure

```
backend/
  bin/migrate.php        creates the database
  database/schema.sql    the links table
  public/index.php       entry point and routing
  src/                   Database, LinkRepository, CodeGenerator
  config.php             settings
frontend/
  src/api.js             fetch wrapper for the backend
  src/App.vue            loads links, holds the page together
  src/components/        ShortenForm and LinkList
```

## Running it locally

You'll need PHP 8.1+ (with pdo_sqlite enabled), Node 22+ and npm.

```bash
# create the database (once)
php backend/bin/migrate.php

# start the API on :8000
php -S localhost:8000 backend/public/index.php

# in another terminal
cd frontend
npm install
npm run dev
```

Then open http://localhost:5173.

On Windows the same commands work in PowerShell; just use backslashes in the
paths if you prefer (`backend\bin\migrate.php`).

## API

| Method | Path          | What it does                                   |
|--------|---------------|------------------------------------------------|
| GET    | `/api/links`  | 20 most recent links                           |
| POST   | `/api/links`  | Body `{ "url": "https://..." }`, returns link  |
| GET    | `/{code}`     | 302 redirect to the original URL, +1 click     |

Invalid URLs get a `422` with an `error` message.

## Some decisions

- **No framework.** The routing is three `if` blocks in `public/index.php`. For
  three routes, a framework would hide more than it helps.
- **SQLite.** No database server to set up, so anyone can clone and run it.
  Because everything goes through PDO, moving to MySQL is mostly a DSN change.
- **Prepared statements everywhere.** All SQL is in `LinkRepository`, so it's
  easy to check there's no string-built query.
- **Short codes** are 6 random characters from an alphabet without lookalikes
  (0/O, 1/l/I). `random_int` is used rather than `rand` because it's
  cryptographically secure. Codes are checked for collisions before saving.
- **Same URL, same code.** Shortening a URL twice returns the existing link
  instead of creating a new one.
- **Validation on both sides.** The browser checks the input is a URL, but the
  backend checks again and only allows http/https, since anything sent to an
  API can skip the frontend.
- **Vite proxy** forwards `/api` to PHP in development, so no CORS headers are needed.

## What I'd add next

- Rate limiting on `POST /api/links`
- Custom aliases (choose your own code)
- Tests (PHPUnit for the repository, Vitest for the components)
- Live click counts without a page refresh
- Styling changes for color contrast, as well as a page snippet preview to view what the shortened URL looks like

## References

Docs and resources I used while building this.

**PHP**
- [PDO](https://www.php.net/manual/en/book.pdo.php) – connecting to SQLite and prepared statements
- [PDOStatement::bindValue](https://www.php.net/manual/en/pdostatement.bindvalue.php) – why `LIMIT` is bound as an integer
- [random_int](https://www.php.net/manual/en/function.random-int.php) – generating the short codes securely
- [Built-in web server](https://www.php.net/manual/en/features.commandline.webserver.php) – running the API with a router script

**SQLite**
- [CREATE TABLE](https://www.sqlite.org/lang_createtable.html) – the schema and `UNIQUE` constraint

**Vue / Vite**
- [Vue: Components Basics](https://vuejs.org/guide/essentials/component-basics.html) – props and emitting events
- [Vue: `<script setup>`](https://vuejs.org/api/sfc-script-setup.html) – Composition API syntax
- [Vue: Lifecycle Hooks](https://vuejs.org/guide/essentials/lifecycle.html) – loading links with `onMounted`
- [Vite: server.proxy](https://vite.dev/config/server-options.html#server-proxy) – sending `/api` to the PHP server

**Web APIs / HTTP (MDN)**
- [Using the Fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API/Using_Fetch)
- [Clipboard.writeText](https://developer.mozilla.org/en-US/docs/Web/API/Clipboard/writeText) – the copy button
- [302 Found](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/302) and [422 Unprocessable Content](https://developer.mozilla.org/en-US/docs/Web/HTTP/Status/422)
- [text-overflow](https://developer.mozilla.org/en-US/docs/Web/CSS/text-overflow) – trimming long URLs in the list

**Security**
- [OWASP SQL Injection Prevention Cheat Sheet](https://cheatsheetseries.owasp.org/cheatsheets/SQL_Injection_Prevention_Cheat_Sheet.html)