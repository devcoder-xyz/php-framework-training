# Le Router et les Routes dans les Frameworks PHP Modernes

Lors du développement d'applications web en PHP, les frameworks modernes intègrent des composants clés tels que le **Router** et les **Routes**. Ces éléments jouent un rôle important dans la gestion des requêtes HTTP et la navigation structurée au sein de l'application.

## Le Router

Le **Router** est un élément central dans un framework PHP moderne, agissant comme un aiguilleur pour diriger les requêtes HTTP vers les parties spécifiques de l'application. Ses principales fonctionnalités sont la correspondance des URLs et la gestion des routes.

### Fonctionnalités du Router

1. **Correspondance des Routes :** Le Router assure la correspondance entre l'URL d'une requête entrante et les routes définies dans l'application.

2. **Gestion des Paramètres :** Il permet de capturer des paramètres dynamiques de l'URL, par exemple, extraire l'identifiant d'un utilisateur à partir de `/users/1`.

3. **Gestion des Méthodes HTTP :** Les frameworks modernes permettent de définir des routes spécifiques pour différentes méthodes HTTP (GET, POST, etc.).

## La Route

La **Route** est une définition qui associe une URL spécifique à une action ou ressource particulière de l'application. Chaque route possède des attributs tels que le chemin, le gestionnaire, et les méthodes HTTP autorisées.

### Caractéristiques des Routes

1. **Chemin (Path) :** Il représente l'URL déclenchant cette route, pouvant contenir des parties statiques et dynamiques.

2. **Handler :** Le gestionnaire est le code exécuté lorsque la route est atteinte, pouvant être un contrôleur, une fonction anonyme ou une méthode de classe.

3. **Méthodes HTTP Autorisées :** Les routes peuvent être limitées à certaines méthodes HTTP, par exemple, uniquement pour les requêtes GET ou POST.

4. **Paramètres Dynamiques :** Les parties du chemin peuvent être définies comme des paramètres dynamiques extraits de l'URL lors de la correspondance.

## Avantages des Routes et du Router

1. **Structure Organisée :** Les routes offrent une structure organisée pour définir la navigation et les actions au sein de l'application.

2. **Maintenabilité :** En centralisant la gestion des routes, le code devient plus maintenable et compréhensible.

3. **Flexibilité :** Les routes permettent de définir des schémas d'URL complexes et de gérer différents comportements en fonction des paramètres.

4. **Réutilisabilité :** Les routes et le Router favorisent la réutilisabilité du code en permettant une définition claire des actions de l'application.



## Création de la Classe Route

**Emplacement du Fichier :** `src/FrameworkPasAPas/Router/Route.php`

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Router;

use InvalidArgumentException;

final class Route
{
    private string $name;
    private string $path;
    private $handler;
    private array $methods = [];

    public function __construct(string $name, string $path, $handler, array $methods = ['GET'])
    {
        if ($methods === []) {
            throw new InvalidArgumentException('Le tableau des méthodes HTTP était vide ; il doit contenir au moins une méthode.');
        }
        $this->name = $name;
        $this->path = self::trimPath($path);
        $this->handler = $handler;
        $this->methods = $methods;
    }

    public function match(string $path, string $method): bool
    {
        if (!in_array($method, $this->getMethods())) {
            return false;
        }

        return self::trimPath($path) == $this->getPath();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    private static function trimPath(string $path): string
    {
        return '/' . rtrim(ltrim(trim($path), '/'), '/');
    }
}
```

**2. Comment Utiliser la Classe Route :**

La classe `Route` permet de définir des routes avec des URI spécifiques, des gestionnaires associés, et des méthodes HTTP autorisées.

```php
// Exemple d'utilisation de la classe Route
$route = new Route('home', '/', [\App\Controller\MainController::class, 'index'], ['GET'])

// Vérifier si une route correspond à une requête
$path = '/contact';
$method = 'GET';
if ($route->match($path, $method)) {
    // La route correspond à la requête
    $name = $route->getName();
    $path = $route->getPath();
    $handler = $route->getHandler();
    $methods = $route->getMethods();
}
```

La classe `Route` simplifie la gestion des routes dans le contexte d'un framework PHP, offrant une abstraction claire pour définir des routes avec des URI spécifiques et des méthodes HTTP autorisées.

## Création de la Classe Router

**Emplacement du Fichier :** `src/FrameworkPasAPas/Router/Router.php`

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Router;

use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Router\Exception\RouteNotFound;

final class Router
{
    private array $routes = [];

    public function __construct(array $routes)
    {
        foreach ($routes as $route) {
            $this->add($route);
        }
    }

    /**
     * @throws RouteNotFound
     */
    public function match(Request $request): Route
    {
        $path = $request->getUri();
        foreach ($this->routes as $route) {
            if ($route->match($path, $request->getMethod()) === false) {
                continue;
            }
            return $route;
        }

        throw new RouteNotFound(
            'Aucune route trouvée pour ' . $path
        );
    }

    public function add(Route $route): self
    {
        $this->routes[] = $route;
        return $this;
    }
}
```

**2. Comment utiliser la Classe Router :**

La classe `Router` permet d'ajouter des routes, de les stocker, et de les faire correspondre avec les requêtes entrantes.

```php
// Exemple d'utilisation de la classe Router
$routes = [
    new Route('home', '/', [\App\Controller\MainController::class, 'index'], ['GET'])
    new Route('contact', '/', [\App\Controller\ContactController::class, 'show'], ['GET'])
    // ... ajouter d'autres routes
];

$router = new Router($routes);

// Correspondre à une route selon une requête
$request = Request::fromGlobals();
try {
    $matchedRoute = $router->match($request);
    $name = $matchedRoute->getName();
    $path = $matchedRoute->getPath();
    $handler = $matchedRoute->getHandler();
    $methods = $matchedRoute->getMethods();
} catch (RouteNotFound $exception) {
    // Gérer l'absence de correspondance
}
```

La classe `Router` simplifie la gestion des routes au sein d'un framework PHP, offrant une abstraction claire pour ajouter des routes, les stocker, et les faire correspondre avec les requêtes entrantes.

## Création de l'Exception RouteNotFound

L'exception `RouteNotFound` est levée lorsqu'aucune route ne correspond à une requête. Elle permet de traiter ce scénario exceptionnel de manière appropriée. Voici comment créer et utiliser cette exception dans le contexte du Framework PHP Pas à Pas :

**1. Structure de l'Exception RouteNotFound (`RouteNotFound.php`):**

**Emplacement du Fichier :** `src/FrameworkPasAPas/Router/Exception/RouteNotFound.php`

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Router\Exception;

final class RouteNotFound extends \Exception
{
}
```

**2. Comment utiliser l'Exception RouteNotFound :**

L'exception `RouteNotFound` peut être utilisée lorsqu'aucune route ne correspond à une requête.

```php
// Exemple d'utilisation de l'exception RouteNotFound
try {
    // ... code du router qui pourrait lever cette exception
    throw new RouteNotFound('Aucune route trouvée pour cette requête');
} catch (RouteNotFound $exception) {
    // Gérer l'absence de correspondance
    header('HTTP/1.0 404 Not Found');
    exit;
}
```
L'exception `RouteNotFound` simplifie la gestion des cas où aucune route ne correspond à une requête dans le contexte d'un framework PHP.

