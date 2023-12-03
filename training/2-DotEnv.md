# Classe DotEnv - Lecture du Fichier .env

## Lecture du Fichier .env

Maintenant que nous avons créé notre fichier `.env`, voyons comment lire et charger ces variables d'environnement dans notre application.

Nous allons utiliser une classe simple appelée `DotEnv`. Voici comment vous pouvez le faire en PHP :

```php
<?php

namespace App\FrameworkPasAPas;

final class DotEnv
{
    protected string $filename;

    public function __construct(string $filename)
    {
        if(!file_exists($filename)) {
            throw new \InvalidArgumentException(sprintf('%s does not exist', $filename));
        }
        $this->filename = $filename;
    }

    public function load() :void
    {
        if (!is_readable($this->filename)) {
            throw new \RuntimeException(sprintf('%s file is not readable', $this->filename));
        }

        $lines = file($this->filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {

            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);

            if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                putenv(sprintf('%s=%s', $name, $value));
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}
```

Explications :

1. **`__construct` :** Le constructeur prend le chemin du fichier `.env` en paramètre. Il vérifie si le fichier existe, sinon, il lance une exception.
2. **`load` :** Cette méthode charge les variables d'environnement du fichier `.env`. Elle lit le fichier ligne par ligne, ignore les commentaires (lignes commençant par `#`), puis sépare le nom et la valeur de chaque variable. Si la variable n'est pas déjà définie, elle la définit dans l'environnement (`putenv`) et dans les superglobales `$_ENV` et `$_SERVER`.
