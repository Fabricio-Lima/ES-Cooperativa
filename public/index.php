<?php

use grupofatec\escooperativa\Controller\Error404Controller;
use grupofatec\escooperativa\Model\Infrastructure\Persistence\MySQLConnectionCreator;
use grupofatec\escooperativa\Model\Infrastructure\Repository\PdoPatrocinadorRepository;
use grupofatec\escooperativa\Model\Infrastructure\Service\RedirectionManager;
use grupofatec\escooperativa\Model\Infrastructure\Service\SessionManager;

require_once __DIR__ . '/../vendor/autoload.php';

SessionManager::sessionStart();
SessionManager::sessionRegenerate();

$requestPath =  $_SERVER['PATH_INFO'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'];
$requiredRoute = "$requestMethod|$requestPath";
$routes = require_once __DIR__ . './../config/routes.php';
$logged = $_SESSION['logado'] ?? false;

if (!$logged && $requestPath !== '/') {
    RedirectionManager::redirect(responseCode: 303);
}

$controllerClass = $routes[$requiredRoute] ?? Error404Controller::class;
switch ($requiredRoute) {
    case 'POST|/':
        $repository = new PdoPatrocinadorRepository(MySQLConnectionCreator::createConnection());
        $controller = new $controllerClass($repository);
        break;
    default:
        $controller = new $controllerClass();
}
$controller->processRequest();