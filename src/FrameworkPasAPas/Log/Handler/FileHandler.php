<?php

namespace App\FrameworkPasAPas\Log\Handler;

use UnexpectedValueException;
use function dirname;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function sprintf;
use function str_replace;

class FileHandler implements HandlerInterface
{
    private string $filename;

    public function __construct(string $filename)
    {
        $dir = dirname($filename);
        if (!file_exists($dir)) {
            $status = mkdir($dir, 0777, true);
            if ($status === false && !is_dir($dir)) {
                throw new UnexpectedValueException(sprintf('There is no existing directory at "%s"', $dir));
            }
        }
        $this->filename = $filename;
    }

    public function handle(array $vars): void
    {
        $output = self::DEFAULT_FORMAT;
        foreach ($vars as $var => $val) {
            $output = str_replace('%' . $var . '%', $val, $output);
        }
        file_put_contents($this->filename, $output . PHP_EOL, FILE_APPEND);
    }
}