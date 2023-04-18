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