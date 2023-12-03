# 7 - Example Usage FrontController With Kernel

### Utilisation du Kernel et de la Request

Nous allons maintenant intégrer le Kernel dans le fichier `index.php`

**1. Fichier `index.php` :**

Emplacement du Fichier : `public/index.php`

```php
<?php

// Inclusion de la classe DotEnv pour charger les variables d'environnement
use App\FrameworkPasAPas\DotEnv;
use App\FrameworkPasAPas\Kernel; // Ajout de l'utilisation de la classe Kernel
use App\FrameworkPasAPas\Http\Request;

// Inclusion de l'autoloader de Composer
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Initialisation de DotEnv pour charger les variables d'environnement
(new DotEnv(dirname(__DIR__) . '/.env'))->load();


// 1. Création d'une instance du Kernel
$kernel = new Kernel(getenv('APP_ENV'));

// 2. Vérification si l'exécution n'est pas en mode CLI
if (php_sapi_name() != 'cli') {
    // 3. Traitement d'une requête HTTP à partir des superglobales
    $request = Request::fromGlobals();
   
    // 4. Appel de la méthode `handle` du Kernel pour traiter la requête
    $response = $kernel->handle($request);

    // 5. Envoi de la réponse générée par le Kernel
    $response->send();
}
```

**2. Exécution :**

- Assurez-vous que le serveur PHP est en cours d'exécution (`php -S localhost:8080 -t public/`).
- Accédez à `http://localhost:8000` dans votre navigateur.

Lorsque vous accédez à la page, le fichier `index.php` crée une instance du Kernel, traite une requête HTTP  à partir des superglobales, et envoie la réponse générée par le Kernel au navigateur.
Vous devriez voir quelque chose comme :

```
Contenu de la réponse
```