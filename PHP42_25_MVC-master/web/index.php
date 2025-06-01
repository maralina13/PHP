<?php

declare(strict_types=1);

use app\controllers\PresentationController;
use app\core\Application;
use app\controllers\HomeController;
use app\controllers\AboutController;
use app\core\exceptions\RouteException;

const PROJECT_ROOT = __DIR__ . "/../";

require PROJECT_ROOT . "vendor/autoload.php";


$application = new Application();

$router = $application->getRouter();

$router->setGetRoute("/", [new PresentationController(), "getView"]);
$router->setPostRoute("/handle", [new PresentationController(), "handleView"]);
$router->setGetRoute("/error", "");
$router->setGetRoute("/home", [new HomeController(), "getView"]);
$router->setGetRoute("/about", [new AboutController(), "getView"]);

ob_start();
$application->run();
ob_flush();


