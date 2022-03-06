<?php
declare(strict_types=1);

namespace Kavan\Routing\Strategy;

use League\Route\Http\Exception;
use League\Route\Strategy\JsonStrategy as BaseJsonStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class JsonStrategy extends BaseJsonStrategy
{
    public function getThrowableHandler(): MiddlewareInterface
    {
        return new class ($this->responseFactory->createResponse()) implements MiddlewareInterface
        {
            protected $response;

            public function __construct(ResponseInterface $response)
            {
                $this->response = $response;
            }

            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler,
            ): ResponseInterface {
                try {
                    return $handler->handle($request);
                } catch (Throwable $exception) {
                    $response = $this->response;
                    $statusCode = $exception instanceof Exception ? $exception->getStatusCode() : 500;

                    $response->getBody()->write(json_encode([
                        'status_code' => $statusCode,
                        'message'     => 'Opa, parece que erramos por aqui!',
                        'reason'      => $exception->getMessage()
                    ]));

                    $response = $response->withAddedHeader('content-type', 'application/json');
                    return $response->withStatus($statusCode, strtok($exception->getMessage(), "\n"));
                }
            }
        };
    }
}
