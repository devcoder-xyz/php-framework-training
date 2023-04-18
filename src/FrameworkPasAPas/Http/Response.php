<?php

namespace App\FrameworkPasAPas\Http;

class Response
{
    public static array $statusTexts = [
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        301 => 'Moved Permanently',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        500 => 'Internal Server Error',
    ];

    private string $content;
    private int $status;
    private string $statusText;
    private array $headers;
    private string $version = '1.0';

    public function __construct(string $content = '', int $status = 200, array $headers = [])
    {
        $this->content = $content;
        $this->status = $status;
        $this->statusText = self::$statusTexts[$status] ?? 'unknown status';
        $this->headers = $headers;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getProtocolVersion(): string
    {
        return $this->version;
    }

    public function getStatusText(): string
    {
        return $this->statusText;
    }

    public function send(): void
    {
        $httpLine = sprintf('HTTP/%s %s %s',
            $this->getProtocolVersion(),
            $this->getStatusCode(),
            $this->getStatusText()
        );

        if (!headers_sent()) {
            header($httpLine, true, $this->getStatusCode());

            foreach ($this->getHeaders() as $name => $value) {
                header("$name: $value", false);
            }
        }

        echo $this->getContent();
    }
}
