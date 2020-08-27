<?php

use App\Action\Auth\LogoutAction;
use App\Action\Auth\TokenCreateAction;
use App\Action\Auth\TokenListAction;
use App\Action\CustomerCreateAction;
use App\Action\CustomerListAction;
use App\Action\CustomerReadAction;
use App\Action\CustomerSearchAction;
use App\Action\Docs\SwaggerUiAction;
use App\Action\HelloAction;
use App\Action\HomeAction;
use App\Action\UserCreateAction;
use App\Action\UserListAction;
use App\Action\UserReadAction;
use App\Action\UserSearchAction;
use App\Middleware\JwtAuthMiddleware;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Interfaces\RouteCollectorProxyInterface as Group; // same as Slim\Routing\RouteCollectorProxy; ?

return function (App $app) {
    // CORS Pre-Flight OPTIONS Request Handler - require use App\Action\PreflightAction ?
    $app->options('/{routes:.*}', function (RequestInterface $request, ResponseInterface $response) {
        return $response;
    });

    // JWT oauth2 API login. This route must not be protected.
    $app->post('/tokens', \App\Action\Auth\TokenCreateAction::class);
    //$app->get('/tokens', \App\Action\Auth\TokenListAction::class); // to build = list of issued tokens and status
    $app->get('/cleanup', \App\Action\Auth\CleanupAction::class);
    $app->get('/logout', \App\Action\Auth\LogoutAction::class);

    // Home
    $app->get('/', \App\Action\HomeAction::class)->setName('root');
    $app->get('/status', \App\Action\HomeAction::class);

    // Hello - group1
    $app->group('/hello', function (Group $group1) {
        $group1->get('', \App\Action\HelloAction::class)->setName('hello');
        $group1->get('/{name}', \App\Action\HelloAction::class)->setName('hello');
    });

    // Customers - group2
    $app->group('/customers', function (Group $group2) {
        $group2->get('', \App\Action\CustomerListAction::class)->setName('customer-list');
        $group2->get('/id/{id:[0-9]+}', \App\Action\CustomerReadAction::class);
        $group2->get('/search/{keyword}', \App\Action\CustomerSearchAction::class);
        $group2->post('', \App\Action\CustomerCreateAction::class);
    });

    // Users - group3
    $app->group('/users', function (Group $group3) {
        $group3->get('', \App\Action\UserListAction::class)->setName('user-list');
        $group3->get('/id/{id:[0-9]+}', \App\Action\UserReadAction::class);
        $group3->get('/search/{keyword}', \App\Action\UserSearchAction::class);
        $group3->post('', \App\Action\UserCreateAction::class);
    })->add(JwtAuthMiddleware::class);

    // Docs - Swagger
    $app->get('/docs/v1', \App\Action\Docs\SwaggerUiAction::class);

    // Route example Multiple optionnal parameters $app->get('/news[/{year}[/{month}]]', function ($request, $response, $args)
    // Route example Unlimited optional parameters $app->get('/news[/{params:.*}]', function ($request, $response, $args) { params = explode('/', $args['params'])

    /*
     * Catch-all route to serve a 404 Not Found page if none of the routes match
     * NOTE: make sure this route is defined last
     */
    $app->map(['GET', 'POST', 'PUT', 'DELETE', 'PATCH'], '/{routes:.+}', function ($request, $response): void {
        throw new Slim\Exception\HttpNotFoundException($request);
    });
};
