<?php

use Slim\Exception\NotFoundException;
use Slim\Http\Request;
use Slim\Http\Response;

$app = require __DIR__ . '/../src/bootstrap.php';

$app->any('/[{func1}[/{func2}[/{func3}]]]', function (Request $request, Response $response, array $args) use($app) {

    $func1 = ucfirst($args['func1']) ?? null;
    $func2 = ucfirst($args['func2']) ?? null;
    $func3 = ucfirst($args['func3']) ?? null;

    $class = '';
    $method = '';
    $pattern = null;

    if ($func1 && $func2 && $func3) {
        // 例）/login/auth/exec → Login\Controller\AuthController#actionExec
        $pattern = 1;
        $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func2 . 'Controller';
        $method = 'action' . $func3;
    } else if ($func1 && $func2) {
        // 例）/login/exec → Login\Controller\ExecController#actionIndex
        $pattern = 2;
        $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func2 . 'Controller';
        $method = 'actionIndex';

        if (!class_exists($class)) {
            // 例）/login/exec → Login\Controller\LoginController#actionExec
            $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func1 . 'Controller';
            $method = 'action' . $func2;
        }
    } else if ($func1) {
        // 例）/login → Login\Controller\LoginController#actionIndex
        $pattern = 3;
        $class = '\\App\\Func\\' . $func1 . '\\Controller\\' . $func1 . 'Controller';
        $method = 'actionIndex';
    }

    if (!class_exists($class)) {
        throw new NotFoundException($request, $response);
    }

    $controller = new $class($app);
    if (!method_exists($controller, $method)) {
        throw new NotFoundException($request, $response);
    }

    if (!$controller->isContinueActionMethod()) {
        return $controller->getResponseIfAbortInConstructor();
    }

    try {
        return $controller->$method();
    } catch (Throwable $exc) {
        $GLOBALS['unexpectedException'] = $exc;
        throw $exc;
    }
});

// Run app
$app->run();
