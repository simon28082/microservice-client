<?php

namespace CrCms\Microservice\Client\Exceptions;

use CrCms\Foundation\ConnectionPool\Exceptions\ConnectionException;
use CrCms\Foundation\ConnectionPool\Exceptions\RequestException;
use CrCms\Microservice\Client\Packer\Packer;
use Illuminate\Http\JsonResponse;
use RuntimeException;
use Exception;
use UnexpectedValueException;

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
        $this->resolveException($exception);
        parent::__construct($exception->getMessage(), $exception->getCode(), $exception);
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
            $this->exceptionMessage = $exception->getMessage();
        } elseif ($exception instanceof RequestException) {
            $this->statusCode = $exception->getConnection()->getStatusCode();
            $content = $exception->getConnection()->getContent();
            if (is_null($content)) {
                $this->exceptionMessage = 'Request error';
            } else {
                $this->exceptionMessage = $this->resolveMessage($content);
            }
        } else {
            $this->exceptionMessage = $exception->getMessage();
            $this->statusCode = $exception->getCode();
        }
    }

    /**
     * @param string $message
     * @return array
     */
    protected function resolveMessage(string $message)
    {
        return app(Packer::class)->unpack($message);
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
            throw new Exception($this->exceptionMessage, $statusCode);
        }
    }
}