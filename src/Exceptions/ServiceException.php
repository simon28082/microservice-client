<?php

namespace CrCms\Foundation\MicroService\Client\Exceptions;

use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\ConnectionPool\Exceptions\RequestException;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Exception;

/**
 * Class ServiceException
 * @package CrCms\Foundation\MicroService\Client\Exceptions
 */
class ServiceException extends RuntimeException
{
    /**
     * @var
     */
    protected $exceptionMessage;

    /**
     * @var
     */
    protected $exceptionCode;

    /**
     * @var
     */
    protected $statusCode = 0;

    /**
     * ServiceException constructor.
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->resolveException($exception);
        parent::__construct($exception->getMessage(), $exception->getCode());
    }

    /**
     * @param Exception $exception
     */
    protected function resolveException(Exception $exception)
    {
        if ($exception instanceof ConnectionException || $exception instanceof RequestException) {
            $this->statusCode = $exception->getConnection()->getStatusCode();
            $this->exceptionMessage = $this->resolveMessage($exception->getConnection()->getContent());
        } else {
            $this->exceptionMessage = $exception->getMessage();
            $this->exceptionCode = $exception->getCode();
        }
    }

    /**
     * @param string $message
     * @return mixed|string
     */
    protected function resolveMessage(?string $message = null)
    {
        $result = json_decode($message, true);
        if (json_last_error() !== 0) {
            $result = $message;
        }

        return $result;
    }

    /**
     * @return JsonResponse
     */
    public function render()
    {
        return new JsonResponse(
            $this->exceptionMessage ? $this->exceptionMessage : 'Bad Gateway',
            $this->statusCode <= 0 ? 502 : $this->statusCode
        );
    }
}