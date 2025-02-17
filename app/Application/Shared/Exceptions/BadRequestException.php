<?php

namespace App\Application\Shared\Exceptions;

use Exception;
use Throwable;

class BadRequestException extends Exception
{
    protected $code = 400;

    protected $message = 'Bad request. Try again';

    public function __construct(string $message = "", ?Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->code;
    }
}
