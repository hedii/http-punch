<?php

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->router->get('/get', function () {
    return new JsonResponse(['message' => 'response ok']);
});

$app->router->post('/post', function () {
    return new JsonResponse(['message' => 'response to post ok']);
});

$app->router->get('/endpoint-ok', function () {
    return new JsonResponse(['message' => 'response ok']);
});

$app->router->get('/endpoint-forbidden', function () {
    return new JsonResponse(['message' => 'forbidden'], Response::HTTP_FORBIDDEN);
});

$app->router->get('/endpoint-error', function () {
    return new JsonResponse(['message' => 'internal error'], Response::HTTP_INTERNAL_SERVER_ERROR);
});

$app->router->get('/endpoint-timeout', function () {
    sleep(2);
    return new JsonResponse(['message' => 'response ok to timeout']);
});

$app->router->get('/endpoint-redirect', function () {
    return new RedirectResponse('/endpoint-ok');
});

$app->run();
