# Container d'Injection de Dépendance 

Le Conteneur d'Injection de Dépendance (DIC) est un composant très utilisé dans le développement d'applications PHP modernes. Il s'agit d'un pattern de conception et d'un conteneur logiciel qui gère la création et l'injection des dépendances entre différentes parties d'une application. L'injection de dépendance est un concept clé dans lequel les dépendances requises par un composant ne sont pas créées à l'intérieur de ce composant, mais sont plutôt injectées depuis l'extérieur.

## Qu'est-ce qu'un Conteneur d'Injection de Dépendance ?

En PHP, un Conteneur d'Injection de Dépendance est souvent une classe ou un composant qui gère la création et la résolution des dépendances d'une application. Les dépendances sont des objets ou des valeurs dont un autre objet a besoin pour fonctionner correctement. Le DIC permet de centraliser la configuration des dépendances et de les fournir aux composants qui en ont besoin.

## Fonctionnement d'un Conteneur d'Injection de Dépendance

Le fonctionnement d'un DIC repose sur le principe de l'inversion de contrôle. Au lieu de créer directement leurs dépendances, les composants les reçoivent du conteneur. Le DIC est responsable de la configuration et de la gestion du cycle de vie des objets. Lorsqu'un composant a besoin d'une dépendance, le DIC la lui fournit.

## Utilisation d'un Conteneur d'Injection de Dépendance

Les avantages de l'utilisation d'un DIC sont nombreux. Ils incluent la facilité de configuration des dépendances, la gestion automatique du cycle de vie des objets, et la possibilité de remplacer facilement les implémentations de dépendances. Les DIC sont particulièrement utiles dans les applications de grande envergure où la gestion des dépendances peut devenir complexe.

## Création d'un Conteneur d'Injection de Dépendance Personnalisé

Pour créer notre propre Conteneur d'Injection de Dépendance (DIC), nous allons suivre quelques étapes. Vous devez créer ces fichiers dans le répertoire `src/FrameworkPasAPas/DependencyInjection`.

### Création du Container (`Container.php`)

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\DependencyInjection;

use App\FrameworkPasAPas\DependencyInjection\Exception\NotFoundException;

final class Container
{
    /**
     * @var array
     */
    private array $definitions = [];

    /**
     * @var array
     */
    private array $resolvedEntries = [];

    public function __construct(array $definitions)
    {
        $this->definitions = array_merge(
            $definitions,
            [self::class => $this]
        );
    }

    /**
     * Récupère une entrée du conteneur.
     *
     * @param string $id Identifiant de l'entrée à rechercher.
     *
     * @return mixed Entrée.
     *
     * @throws NotFoundException  Aucune entrée n'a été trouvée.
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("No entry or class found for '$id'");
        }

        if (array_key_exists($id, $this->resolvedEntries)) {
            return $this->resolvedEntries[$id];
        }

        $value = $this->definitions[$id];
        if ($value instanceof \Closure) {
            $value = $value($this);
        }

        $this->resolvedEntries[$id] = $value;
        return $value;
    }

    /**
     * Vérifie si une entrée existe dans le conteneur.
     *
     * @param string $id Identifiant de l'entrée à rechercher.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return array_key_exists($id, $this->definitions) || array_key_exists($id, $this->resolvedEntries);
    }
}
```

Ce fichier définit la classe `Container`, qui gère la résolution des dépendances.

### Création de l'Exception (`NotFoundException.php`)

```php
<?php

namespace App\FrameworkPasAPas\DependencyInjection\Exception;

class NotFoundException extends \InvalidArgumentException
{
}
```

Ce fichier définit une exception `NotFoundException` qui sera levée lorsque le conteneur ne peut pas trouver une entrée spécifiée.

## Utilisation du Conteneur dans l'Application

Créez un fichier `services.php` dans le dossier `config` de votre application avec le contenu suivant :

```php
<?php

use App\Controller\MainController;
use App\FrameworkPasAPas\Controller\ControllerMiddleware;
use App\FrameworkPasAPas\DependencyInjection\Container;
use App\FrameworkPasAPas\Renderer\PHPRenderer;
use App\FrameworkPasAPas\Router\Router;
use App\FrameworkPasAPas\Router\RouterMiddleware;

return [
    MainController::class => static function (Container $container) {
        return new MainController();
    },
    RouterMiddleware::class => static function (Container $container) {
        return new RouterMiddleware($container->get(Router::class));
    },
    ControllerMiddleware::class => static function (Container $container) {
        return new ControllerMiddleware($container);
    },
    Router::class => static function (Container $container) {
        return new Router(require 'routes.php');
    },
];
```
Modifiez le constructeur de la classe `RouterMiddleware::class` comme suit :

```php
//    Ancienne implémentation
//    public function __construct()
//    {
//        $this->router = new Router(require dirname(__DIR__, 3).'/config/routes.php');
//    }

    // Nouvelle implémentation en utilisant l'injection de dépendances
    public function __construct(Router $router)
    {
        $this->router = $router;
    }
```
Ajoutez le constructeur dans `ControllerMiddleware::class` comme suit :

```php
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
```

Et modifiez la method `resolveController` de la classe `ControllerMiddleware::class` comme suit :

```php
    // Ancienne implémentation
    //private function resolveController(Request $request): callable
    //{
    //    $controller = $request->getAttribute(self::CONTROLLER);
    //    if (is_string($controller)) {
    //        $controller = new $controller();
    //    }
    //
    //    if (is_callable($controller)) {
    //        return $controller;
    //    }
    //
    //    $action = $request->getAttribute(self::ACTION);
    //    if (method_exists($controller, $action) === false) {
    //        throw new BadMethodCallException(
    //            $action === null
    //                ? sprintf('Please use a Method on class %s.', get_class($controller))
    //                : sprintf('Method "%s" on class %s does not exist.', $action, get_class($controller))
    //        );
    //    }
    //    return [$controller, $action];
    //}
    
    // Nouvelle implémentation
    private function resolveController(Request $request): callable
    {
        $_controller = $request->getAttribute(self::CONTROLLER);
        if (is_callable($_controller)) {
            return $_controller;
        }

        $controller = $this->container->get($_controller);
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
```