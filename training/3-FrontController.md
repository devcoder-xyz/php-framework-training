# Front Controller

## Création du Front Controller (public/index.php)
Le Front Controller est un point d'entrée unique pour toutes les requêtes entrantes dans une application PHP. Créons le nôtre dans le fichier `public/index.php`. 

```php
<?php

// Inclusion de la classe DotEnv pour charger les variables d'environnement
use App\FrameworkPasAPas\DotEnv;

// Inclusion de l'autoloader de Composer
require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

// Initialisation de DotEnv pour charger les variables d'environnement
(new DotEnv(dirname(__DIR__) . '/.env'))->load();

// À partir de ce point, vous pouvez initialiser votre application, définir des routes, etc.

// Exemple : Initialisation d'un simple "Hello World" pour tester le Front Controller
echo 'Hello World!';

```

Explications :

1. **Inclusion de la classe DotEnv :** Nous utilisons la classe `DotEnv` que nous avons créée précédemment pour charger les variables d'environnement depuis le fichier `.env`.

2. **Inclusion de l'autoloader de Composer :** Nous incluons l'autoloader généré par Composer, ce qui nous permet d'utiliser des classes et des dépendances définies dans notre projet.

3. **Initialisation de DotEnv :** Nous créons une instance de la classe `DotEnv` et appelons sa méthode `load()` pour charger les variables d'environnement.

4. **Initialisation de l'Application :** À partir de ce point, vous pouvez initialiser votre application, définir des routes, configurer des dépendances, etc. L'exemple ici affiche simplement "Hello World" à des fins de test.

### Test du DotEnv - Exemple Pratique

1. **Modifier le Fichier .env :**

   Ajoutez une nouvelle variable d'environnement dans le fichier `.env` à la racine du projet.

   ```plaintext
   APP_ENV=dev
   ```

2. **Test dans le Front Controller :**

   Modifiez votre fichier `public/index.php` pour inclure un test avec la nouvelle variable d'environnement.

   ```php
   <?php

 
   use App\FrameworkPasAPas\DotEnv;

   require dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


   (new DotEnv(dirname(__DIR__) . '/.env'))->load();
   $appEnv = getenv('APP_ENV');

   // Affichage du résultat
   echo "La variable d'environnement APP_ENV est : $appEnv";
   ```

3. **Exécution du Test :**

    - Assurez-vous que votre serveur local est en cours d'exécution.
    - Ouvrez votre navigateur et accédez à votre projet.

   Vous devriez voir quelque chose comme :

   ```
   La variable d'environnement APP_ENV est : dev
   ```