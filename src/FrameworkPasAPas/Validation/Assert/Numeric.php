<?php

declare(strict_types=1);

namespace App\FrameworkPasAPas\Validation\Assert;

use function is_numeric;

final class Numeric extends AbstractValidator
{
    /**
     * @var string
     */
    private string $message = 'This value should be of type {{ type }}.';

    public function validate($value): bool
    {
        if ($value === null) {
            return true;
        }

        if (is_numeric($value) === false) {
            $this->error($this->message, ['value' => $value, 'type', 'numeric']);
            return false;
        }

        return true;
    }

    public function message(string $message): self
    {
        $this->message = $message;
        return $this;
    }
}
