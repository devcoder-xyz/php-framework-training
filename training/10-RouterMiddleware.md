# RouterMiddleware

Le **RouterMiddleware** sert à traiter les requêtes HTTP d'un framework PHP, permettant la correspondance des requêtes
avec des routes spécifiques. Son rôle est de préparer la requête en extrayant les informations pertinentes de la route,
telles que le contrôleur, l'action, et le nom de la route.

### Fonctionnalités du RouterMiddleware

1. **Correspondance des Routes :** Utilise le Router pour identifier la route correspondante à la requête.

2. **Préparation des Attributs :** Extrait les données importantes de la route et les ajoute comme attributs à la
   requête. Ces attributs sont souvent utilisés par d'autres composants, tels que les contrôleurs, pour déterminer le
   traitement à effectuer.

3. **Gestion des Exceptions :** En cas de non-correspondance avec une route, capture l'exception `RouteNotFound` et
   retourne une réponse HTTP 404 pour indiquer que la ressource demandée n'a pas été trouvée.

### Défintions des routes

Pour configurer les routes de votre application, créez un fichier `config/routes.php` à la racine de votre projet. C'est
dans ce fichier que vous définirez les différentes routes que votre application doit gérer.

Dans cet exemple, nous limitons l'acceptation uniquement aux fonctions anonymes. Les contrôleurs seront abordés dans les prochains chapitres.

```php
<?php

return [
    new \App\FrameworkPasAPas\Router\Route('app_index', '/', function () {
        return new \App\FrameworkPasAPas\Http\Response("Page d'accueil");
    }, ['GET']),
        new \App\FrameworkPasAPas\Router\Route('app_contact', '/contact', function () {
        return new \App\FrameworkPasAPas\Http\Response("Page contact");
    }, ['GET']),
    // Ajoutez d'autres routes au besoin
];
```

### Implémentation du RouterMiddleware

La classe `RouterMiddleware` est implémentée comme suit :

**Emplacement du Fichier :** `src/FrameworkPasAPas/Router/RouterMiddleware.php`

```php
<?php 

namespace App\FrameworkPasAPas\Router;

use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use App\FrameworkPasAPas\Kernel;
use App\FrameworkPasAPas\Router\Exception\RouteNotFound;

final class RouterMiddleware implements MiddlewareInterface
{
    private Router $router;

    public function __construct()
    {
        $this->router = new Router(require dirname(__DIR__, 3).'/config/routes.php');
    }

    public function process(Request $request): ?Response
    {
        try {
            $route = $this->router->match($request);
            
            $routeHandler = $route->getHandler();
            if (is_callable($routeHandler)) {
                return $routeHandler();
            }
            
            // Si le gestionnaire n'est pas un callable, une exception logique est levée
            throw new \LogicException('Handler must be a callable.');
           
        } catch (RouteNotFound $exception) {
            return new Response(null, 404);
        }

        return null;
    }
}

```

### Configuration dans `config/middlewares.php`

Pour intégrer le `RouterMiddleware` dans votre framework PHP, ajoutez-le à la configuration des middlewares
dans `config/middlewares.php` :

```php
<?php

return [
    new \App\FrameworkPasAPas\Router\RouterMiddleware(),
    // Autres middlewares...
];
```

## Exécution

- Assurez-vous que le serveur PHP est en cours d'exécution (`php -S localhost:8080 -t public/`).
- Accédez à `http://localhost:8000` dans votre navigateur.
Vous devriez voir quelque chose comme :

```
Page d'accueil
```

- Accédez à `http://localhost:8000/contact` dans votre navigateur.
  Vous devriez voir quelque chose comme :

```
Page contact
```
