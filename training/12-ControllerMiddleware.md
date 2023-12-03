# ControllerMiddleware

Le **ControllerMiddleware** sert à traiter des informations extraites par le **RouterMiddleware**. Il est spécifiquement conçu pour exécuter le contrôleur approprié, la bonne action, et, le cas échéant, injecter les arguments nécessaires dans la méthode appelée. Ce middleware agit comme un maillon central assurant la liaison entre les routes identifiées et l'exécution des actions correspondantes.

### Fonctionnalités du ControllerMiddleware

1. **Invocation du contrôleur :** Utilise les informations extraites de la route pour invoquer le contrôleur associé à la requête.

2. **Exécution de l'action :** Si spécifiée, déclenche l'exécution de l'action du contrôleur correspondant à la route.

3. **Préparation des attributs :** Extrayant les attributs de la requête préparée par le router, le **ControllerMiddleware** injecte les arguments nécessaires dans la méthode appelée.

## Création du fichier `ControllerMiddleware.php`

Pour intégrer le **ControllerMiddleware** dans un framework PHP, suivez ces étapes pour créer le fichier correspondant.

1. **Emplacement du fichier :** `src/FrameworkPasAPas/Controller/ControllerMiddleware.php`

2. **Contenu du fichier :**
```php
<?php

namespace App\FrameworkPasAPas\Controller;

use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use BadMethodCallException;
use LogicException;

final class ControllerMiddleware implements MiddlewareInterface
{
    public const CONTROLLER = '_controller';
    public const ACTION = '_action';
    public const NAME = '_name';

    public function process(Request $request): ?Response
    {
        $controller = $this->resolveController($request);
        return self::callController($request, $controller);
    }

    private function resolveController(Request $request): callable
    {
        $controller = $request->getAttribute(self::CONTROLLER);
        if (is_string($controller)) {
            $controller = new $controller();
        }

        if (is_callable($controller)) {
            return $controller;
        }

        $action = $request->getAttribute(self::ACTION);
        if (method_exists($controller, $action) === false) {
            throw new BadMethodCallException(
                $action === null
                    ? sprintf('Please use a Method on class %s.', get_class($controller))
                    : sprintf('Method "%s" on class %s does not exist.', $action, get_class($controller))
            );
        }
        return [$controller, $action];
    }

    private static function callController(Request $request, $controller): Response
    {
        $attributes = $request->getAttributes();
        unset($attributes[self::CONTROLLER]);
        unset($attributes[self::ACTION]);
        unset($attributes[self::NAME]);

        $response = $controller(...array_values($attributes));
        if (!$response instanceof Response) {
            throw new LogicException(
                'The controller must return an instance of ' . Response::class
            );
        }
        return $response;
    }
}
```

3. **Explication du Contenu :**
    - Ce fichier PHP définit une classe `ControllerMiddleware` qui implémente l'interface `MiddlewareInterface`.
    - La classe contient des constantes (`CONTROLLER`, `ACTION`, `NAME`) utilisées comme clés d'attributs pour la requête.
    - Les méthodes `resolveController` et `callController` sont utilisées pour gérer la résolution et l'invocation du contrôleur.
    - Les exceptions sont levées en cas de conditions invalides, assurant une gestion appropriée des erreurs.
    - La méthode `process` est appelée lors du passage de la requête dans le middleware, déclenchant l'exécution du contrôleur.


## Intégration dans le Framework

- Pour utiliser ce middleware, ajoutez-le à la configuration des middlewares dans `config/middlewares.php`
```php
<?php

return [
    new \App\FrameworkPasAPas\Router\RouterMiddleware(),
    new \App\FrameworkPasAPas\Controller\ControllerMiddleware(),
];
```

### Modification du RouterMiddleware pour intégrer le ControllerMiddleware

Pour intégrer le **ControllerMiddleware** dans le **RouterMiddleware**, suivez ces étapes :

1. **Emplacement du fichier RouterMiddleware :** Assurez-vous que le fichier `RouterMiddleware.php` est situé dans le bon emplacement, `src/FrameworkPasAPas/Router/RouterMiddleware.php`.

2. **Modification du contenu du fichier :**
```php
<?php

namespace App\FrameworkPasAPas\Router;

use App\FrameworkPasAPas\Controller\ControllerMiddleware;
use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
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
            $attributes = [
                ControllerMiddleware::CONTROLLER => $routeHandler[0] ?? $routeHandler,
                ControllerMiddleware::ACTION => $routeHandler[1] ?? null,
                ControllerMiddleware::NAME => $route->getName(),
            ];

            foreach ($attributes as $key => $value) {
               $request->withAttribute($key, $value);
            }
        } catch (RouteNotFound $exception) {
            return new Response(null, 404);
        }

        return null;
    }
}
```

3. **Explication de la Modification :**
    - Dans la méthode `process`, après avoir obtenu matcher la route, nous extrayons les informations nécessaires du router et les ajoutons comme attributs à la requête en utilisant les constantes définies dans `ControllerMiddleware`.
    - Cette etape permet au **RouterMiddleware** de préparer la requête en ajoutant les informations du contrôleur, de l'action et du nom de la route en tant qu'attributs. Ces informations sont ensuite utilisées par le **ControllerMiddleware** pour invoquer le contrôleur approprié lors du traitement ultérieur de la requête.

