<?php

namespace CrCms\Microservice\Client\Exceptions;

use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\ConnectionPool\Exceptions\RequestException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Exception;

/**
 * Class ServiceException
 * @package CrCms\Microservice\Client\Exceptions
 */
class ServiceException extends RuntimeException
{
    /**
     * @var array|string
     */
    protected $exceptionMessage;

    /**
     * @var int
     */
    protected $statusCode = 0;

    /**
     * @var Exception
     */
    protected $exception;

    /**
     * ServiceException constructor.
     * @param Exception $exception
     */
    public function __construct(Exception $exception)
    {
        $this->exception = $exception;
        $this->resolveException($exception);
        parent::__construct($exception->getMessage(), $exception->getCode());
    }

    /**
     * @return array|string
     */
    public function getExceptionMessage()
    {
        return $this->exceptionMessage ? $this->exceptionMessage : 'Gateway error';
    }

    /**
     * @return int
     */
    public function getExceptionStatusCode(): int
    {
        return $this->statusCode <= 0 ? 502 : $this->statusCode;
    }

    /**
     * @param Exception $exception
     */
    protected function resolveException(Exception $exception)
    {
        if ($exception instanceof ConnectionException) {
            $this->statusCode = $exception->getConnection()->getStatusCode();
            $this->exceptionMessage = $this->resolveMessage(strval($exception->getMessage()));
        } elseif ($exception instanceof RequestException) {
            $this->statusCode = $exception->getConnection()->getStatusCode();
            $this->exceptionMessage = $this->resolveMessage(strval($exception->getConnection()->getContent()));
            if (is_array($this->exceptionMessage) && empty($this->exceptionMessage['message'])) {
                $this->exceptionMessage = $this->resolveMessage(strval($exception->getMessage()));
            }
        } else {
            $this->exceptionMessage = $exception->getMessage();
            $this->statusCode = $exception->getCode();
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
     * @throws Exception
     */
    public function render()
    {
        $statusCode = $this->getExceptionStatusCode();
        if (is_array($this->exceptionMessage)) {
            return new JsonResponse(
                $this->exceptionMessage,
                $statusCode
            );
        } else {
            throw new Exception($this->exceptionMessage ? $this->exceptionMessage : $this->exception->getMessage(),
                $statusCode);
        }
    }
}