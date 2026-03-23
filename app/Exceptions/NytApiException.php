<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class NytApiException extends Exception
{
    public function __construct(
        string $message,
        int $code = 0,
        private ?Response $response = null
    ) {
        parent::__construct($message, $code);
    }

    public function getResponse(): ?Response
    {
        return $this->response;
    }
}
