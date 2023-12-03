# Middleware dans un Environnement PHP

Les middleware occupent une place essentielle dans le processus de développement des applications web en PHP. Ils proposent une approche souple et modulaire pour intervenir dans le traitement des requêtes HTTP avant qu'elles n'atteignent le cœur de la logique métier de votre application. Ces composants intermédiaires ont la capacité de manipuler tant la requête que la réponse, et peuvent également agir comme des filtres, conditionnant ainsi l'exécution du code principal.
## Qu'est-ce qu'un Middleware ?

En PHP, un middleware représente une couche placée entre le serveur web et votre application. Cette couche agit comme un tampon qui a la capacité d'intercepter, traiter, ou transformer la requête avant qu'elle n'atteigne la logique métier de votre application. De manière similaire, le middleware peut également manipuler la réponse avant qu'elle ne soit renvoyée au client.

## Fonctionnement d'un Middleware

Le fonctionnement d'un middleware repose souvent sur le principe d'une pile (stack) où chaque middleware a l'occasion de traiter la requête et la réponse de manière séquentielle. La requête traverse chaque middleware successivement, puis la réponse est renvoyée en sens inverse. Chaque middleware peut prendre des décisions, apporter des modifications, voire interrompre le flux normal du traitement.
## Utilisation des Middleware

Les middleware sont polyvalents et peuvent être exploités pour différentes tâches telles que l'authentification, l'autorisation, la gestion des sessions, la journalisation, la mise en cache, la compression des réponses, etc. Ils sont souvent utilisés pour isoler des aspects spécifiques du traitement des requêtes et des réponses, facilitant ainsi la réutilisation du code.
## Implémentation des Middleware

En PHP, l'implémentation des middleware peut varier. Certains frameworks PHP fournissent des systèmes de middleware intégrés, tandis que d'autres laissent aux développeurs la liberté de définir leur propre structure. Les middleware sont généralement des classes qui implémentent une interface spécifique, telle que `MiddlewareInterface`.

```php
<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface MiddlewareInterface
{
    public function process(ServerRequestInterface $request, callable $next): ResponseInterface;
}
```

Un middleware doit implémenter une méthode `process` qui prend la requête et une fonction `$next` comme arguments. La fonction `$next` représente l'appel au middleware suivant dans la pile. Un middleware peut décider d'appeler `$next` pour passer la requête au middleware suivant ou de retourner directement une réponse pour mettre fin au flux.

## Exemple d'utilisation Concret

Plongeons dans un exemple pratique de middleware qui simule un processus d'authentification basique.

Voici un middleware appelé `AuthenticationMiddleware` :

```php
<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthenticationMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, callable $next): ResponseInterface
    {
        // Simuler un processus d'authentification basique
        $authenticated = $this->authenticateUser($request);

        if (!$authenticated) {
            // En cas d'échec de l'authentification, renvoyer une réponse d'erreur
            return new ErrorResponse('Unauthorized', 401);
        }

        // Appeler le middleware suivant dans la pile
        return $next($request);
    }

    private function authenticateUser(ServerRequestInterface $request): bool
    {
        // Implémentation de l'authentification ici (ex: vérification du jeton, connexion à la base de données, etc.)
        // Pour cet exemple, considérons simplement que l'utilisateur est authentifié.
        return true;
    }
}
```

Dans cet exemple, le middleware `AuthenticationMiddleware` simule un processus d'authentification. Il vérifie si l'utilisateur est authentifié en appelant une fonction `authenticateUser`. Si l'authentification réussit, le middleware appelle le middleware suivant dans la pile en utilisant `return $next($request)`. Sinon, il renvoie une réponse d'erreur.

Cela démontre comment les middlewares peuvent être utilisés pour des tâches telles que l'authentification, fournissant ainsi un mécanisme flexible pour gérer le flux de traitement des requêtes dans une application PHP.

## Intégration des Middlewares dans le Kernel

Actuellement, le kernel est capable de traiter la requête et de renvoyer une réponse, mais il ne prend pas en compte les interventions spécifiques que les middlewares peuvent apporter.


Créons une interface `MiddlewareInterface` dans le répertoire `src/FrameworkPasAPas/Http`. Vous pouvez ajouter ce fichier comme suit :

```php
// src/FrameworkPasAPas/Http/MiddlewareInterface.php

<?php

namespace App\FrameworkPasAPas\Http;

interface MiddlewareInterface
{
    public function process(Request $request): ?Response;
}
```

Cette interface définit un contrat pour les classes de middleware, exigeant qu'elles aient une méthode `process` prenant une instance de `Request` en paramètre et retournant éventuellement une instance de `Response`. Cette interface sera implémentée par tous les middlewares que vous créerez dans votre application.

Modifions le fichier `Kernel.php` situé dans le répertoire `src` pour qu'il intègre la gestion des middlewares :

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas;

use App\FrameworkPasAPas\Http\MiddlewareInterface;
use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use function dirname;

final class Kernel
{
    private string $environment;
    private array $middlewareCollection = [];

    public function __construct(string $environment)
    {
        $this->environment = $environment;
        \error_reporting(0);
        if ($environment === 'dev') {
            \error_reporting(E_ALL);
            \ini_set('display_errors', '1');
        }
        $this->boot();
    }

    // La méthode boot a été modifiée pour initialiser les middlewares
    private function boot(): void
    {
        date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
        $this->initializeMiddlewares(); // Appel de la nouvelle méthode
    }

    // La méthode handle a été ajustée pour itérer sur les middlewares et les exécuter
    public function handle(Request $request): Response
    {
        $response = null;
        foreach ($this->middlewareCollection as $middleware) {
            if (is_string($middleware)) {
                $middleware = new $middleware();
            }

            if (!$middleware instanceof MiddlewareInterface) {
                throw new \LogicException(
                    sprintf(
                        'The Middleware must be an instance of %s, "%s" given.',
                        MiddlewareInterface::class,
                        is_object($middleware) ? get_class($middleware) : gettype($middleware)
                    )
                );
            }

            $response = $middleware->process($request);
            if ($response instanceof Response) {
                break;
            }
        }
        return $response;
    }

    public function getParameters(): array
    {
        return [
            'kernel.project_dir' => $this->getProjectDir(),
            'kernel.environment' => $this->environment,
        ];
    }

    public function getProjectDir(): string
    {
        return dirname(__DIR__, 2);
    }

    // Nouvelle méthode pour initialiser les middlewares à partir d'un fichier de configuration
    private function initializeMiddlewares(): void
    {
        $this->middlewareCollection = require $this->getProjectDir() . '/config/middlewares.php';
    }
}
```
Et créons un fichier `config/middlewares.php` initial contenant :

```php
<?php

// config/middlewares.php

return [
    // Liste des middlewares à configurer
];
```

Explications des modifications :

1. **Constructeur (`__construct`) :** Le constructeur prend désormais l'environnement en paramètre et initialise la collection de middlewares.

2. **Méthode `boot` :** La méthode `boot` appelle maintenant la nouvelle méthode `initializeMiddlewares` pour initialiser les middlewares.

3. **Méthode `handle` :** La méthode `handle` a été ajustée pour itérer sur les middlewares et les exécuter. Elle vérifie également si chaque middleware est une instance de l'interface `MiddlewareInterface`.

4. **Nouvelles méthodes (`getParameters`, `getProjectDir`, `initializeMiddlewares`) :** Ces méthodes ont été ajoutées pour fournir des fonctionnalités spécifiques au Kernel liées à la gestion des middlewares.

5. **Propriété `middlewareCollection` :** C'est la collection qui stocke les middlewares chargés à partir du fichier de configuration.

Toutes ces modifications permettent au Kernel de travailler avec un ensemble de middlewares, offrant ainsi une flexibilité accrue au framework. Pour des raisons de simplicité dans nos exemples et cours, nous choisissons de ne pas utiliser la fonction `next`, rendant ainsi les middlewares plus accessibles aux débutants.