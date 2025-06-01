<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Application;

class HomeController
{
    public function getView(): void
    {
        Application::$app->getRouter()->renderView("home");
    }
}