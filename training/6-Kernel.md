# Le Kernel

Le Kernel est le cœur du framework, responsable du traitement des requêtes HTTP, de l'initialisation de l'application et de la coordination des différentes parties du système. Il est configuré en fonction de l'environnement de développement (`dev`, `prod`, etc.), permettant un comportement spécifique en fonction des besoins.

**Structure de la Classe Kernel (`Kernel.php`):**

**Emplacement du Fichier :** `src/FrameworkPasAPas/Kernel.php`

```php
<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas;

use App\FrameworkPasAPas\Http\Request;
use App\FrameworkPasAPas\Http\Response;
use function dirname;

final class Kernel
{
    private string $environment;

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

    private function boot(): void
    {
        date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
    }

    public function handle(Request $request): Response
    {
        return new Response('Contenu de la réponse', 200, ['Content-Type' => 'text/html']);

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
}
```

### Compréhension du Code

- **Constructeur :**
  ```php
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
  ```
    - Le constructeur prend en paramètre l'environnement dans lequel le Kernel fonctionne. Il initialise également les paramètres d'erreur en fonction de l'environnement.

- **Initialisation (boot) :**
  ```php
  private function boot(): void
  {
      date_default_timezone_set(getenv('APP_TIMEZONE') ?: 'UTC');
  }
  ```
    - La méthode `boot` configure le fuseau horaire en fonction de la variable d'environnement `APP_TIMEZONE`.

- **Gestionnaire de Requêtes (handle) :**
  ```php
  public function handle(Request $request): Response
  {
      return new Response('Contenu de la réponse', 200, ['Content-Type' => 'text/html']);
  }
  ```
    - Le gestionnaire de requêtes (`handle`) est pour l'instant simplifié et renvoie une nouvelle instance de la classe `Response`. Dans une application réelle, cette méthode traiterait la requête, invoquerait le contrôleur approprié, et retournerait la réponse générée.

- **Récupération des Paramètres :**
  ```php
  public function getParameters(): array
  {
      return [
          'kernel.project_dir' => $this->getProjectDir(),
          'kernel.environment' => $this->environment,
      ];
  }
  ```
    - La méthode `getParameters` renvoie un tableau associatif contenant des informations importantes telles que le répertoire du projet et l'environnement.

- **Répertoire du Projet :**
  ```php
  public function getProjectDir(): string
  {
      return dirname(__DIR__, 2);
  }
  ```
    - La méthode `getProjectDir` retourne le chemin absolu vers le répertoire du projet en utilisant `dirname`.

### Conclusion

Bien que le Kernel soit actuellement rudimentaire, il sert de base pour la gestion des requêtes et des paramètres essentiels. Dans les sections suivantes, nous explorerons l'intégration des services, des middlewares et d'autres composants pour construire un framework robuste et extensible.