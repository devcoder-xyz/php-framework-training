<?php

namespace App\FrameworkPasAPas\Http;

class JsonResponse extends Response
{
    public function __construct(?array $data = null, int $status = 200, int $flags = JSON_HEX_TAG
    | JSON_HEX_APOS
    | JSON_HEX_AMP
    | JSON_HEX_QUOT
    | JSON_UNESCAPED_SLASHES)
    {
        parent::__construct(json_encode($data, $flags), $status, ['Content-Type' => 'application/json']);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException(
                sprintf('Unable to encode data to JSON : %s', json_last_error_msg())
            );
        }
    }
}
