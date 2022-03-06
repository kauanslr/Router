# Routing
Simple package made on top of league/route to handle routing in HTTP Requests.

## Usage

```php
<?php declare(strict_types=1);

include 'path/to/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

$httpApp = new Kavan\Routing\HttpApplication();

$httpApp->router->post('/user', function(ServerRequestInterface $request): ResponseInterface {
    $response = new Laminas\Diactoros\Response\JsonResponse;
    
    $response->setBody([
        'foo' => 'bar'
    ]);
    
    return $response;
});

$httpApp->start(); 

```

