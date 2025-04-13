<?php

namespace App\Application\Shared\Exceptions;

use Exception;
use Throwable;

class ConflictHttpException extends Exception
{
    protected $code = 409;

    protected $message = 'Conflict error. Try again';

    public function __construct(string $message = '', ?Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }
}
