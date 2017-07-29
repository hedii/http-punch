<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$app = new Laravel\Lumen\Application(
    realpath(__DIR__ . '/../')
);

$app->get('/get', function () {
    return response()->json(['message' => 'response ok']);
});

$app->post('/post', function () {
    return response()->json(['message' => 'response to post ok']);
});

$app->get('/endpoint-ok', function () {
    return response()->json(['message' => 'response ok'], 200);
});

$app->get('/endpoint-forbidden', function () {
    return response()->json(['message' => 'forbidden'], 403);
});

$app->get('/endpoint-error', function () {
    return response()->json(['message' => 'internal error'], 500);
});

$app->get('/endpoint-timeout', function () {
    sleep(2);
    return response()->json(['message' => 'response ok to timeout']);
});

$app->get('/endpoint-redirect', function () {
    return redirect('/endpoint-ok');
});

$app->run();
