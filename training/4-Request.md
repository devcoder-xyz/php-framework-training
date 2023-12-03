# HTTP - REQUEST

## Création de la Classe Request

Dans le développement d'un framework PHP, la classe `Request` joue un rôle central pour gérer et encapsuler les données de la requête HTTP. Elle offre des méthodes pratiques pour accéder aux différentes parties d'une requête, telles que les paramètres GET, POST, les cookies, les fichiers, etc. Voici comment créer et utiliser cette classe :

**1. Structure de la Classe Request (`Request.php`):**

**Emplacement du Fichier :** `src/FrameworkPasAPas/Http/Request.php`

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Http;

final class Request
{
    private array $query;
    private array $request;
    private array $cookies;
    private array $files;
    private array $server;
    private array $attributes;

    public function __construct(array $server, array $query = [], array $request = [], array $cookies = [], array $files = [])
    {
        $this->query = $query;
        $this->request = $request;
        $this->cookies = $cookies;
        $this->files = $files;
        $this->server = $server;
    }

    public static function fromGlobals(): self
    {
        return new self($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
    }

    public function getUri(): string
    {
        return $this->server['REQUEST_URI'];
    }

    public function getMethod(): string
    {
        return $this->server['REQUEST_METHOD'];
    }

    public function isMethodPost(): bool
    {
        return $this->getMethod() === 'POST';
    }

    public function getQuery(): array
    {
        return $this->query;
    }

    public function getRequest(): array
    {
        return $this->request;
    }

    public function getCookies(): array
    {
        return $this->cookies;
    }

    public function getFiles(): array
    {
        return $this->files;
    }

    public function withAttribute($key, $value): self
    {
        $this->attributes[$key] = $value;
        return $this;
    }

    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
```

**2. Comment Utiliser la Classe Request :**

La classe `Request` est utilisée pour encapsuler les informations de la requête HTTP. Vous pouvez l'instancier directement ou utiliser la méthode statique `fromGlobals` pour créer une instance à partir des variables globales PHP.

```php
// Exemple d'utilisation de la classe Request
$request = new Request($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);

// Ou, utilisez la méthode statique pour créer une instance basée sur les variables globales
$request = Request::fromGlobals();

// Accéder aux différentes parties de la requête
$uri = $request->getUri();
$method = $request->getMethod();
$isPost = $request->isMethodPost();
$queryParams = $request->getQuery();
$requestParams = $request->getRequest();
$cookies = $request->getCookies();
$files = $request->getFiles();

// Gérer les attributs de la requête
$request = $request->withAttribute('key', 'value');
$attributeValue = $request->getAttribute('key');
$allAttributes = $request->getAttributes();
```

Cette classe `Request` simplifie la gestion des requêtes HTTP dans le contexte d'un framework PHP, offrant une abstraction claire pour accéder aux différentes parties d'une requête.