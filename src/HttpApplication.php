<?php

declare(strict_types=1);

namespace Kavan\Routing;

use Kavan\Routing\Strategy\JsonStrategy;
use League\Route\Router;
use Laminas\Diactoros\ResponseFactory;
use League\Route\Strategy\StrategyInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Container\ContainerInterface;

class HttpApplication
{
    public function __construct(
        public ContainerInterface $container,
        public Router $router = new Router()
    ) {
        $this->configureRouter();
    }

    public function start(): void
    {
        $request = ServerRequestFactory::fromGlobals(
            $_SERVER, $_GET, $_POST, $_COOKIE, $_FILES
        );
        
        $response = $this->router->dispatch($request);
        
        // send the response to the browser
        (new SapiEmitter)->emit($response);
    }

    private function configureRouter(
        ResponseFactoryInterface $responseFactory = new ResponseFactory,
        StrategyInterface $strategy = null
    ): void {
        $strategy ??= new JsonStrategy($responseFactory);
        $responseFactory = new ResponseFactory();

        $strategy->setContainer($this->container);
        $this->router->setStrategy($strategy); 
    }
}
