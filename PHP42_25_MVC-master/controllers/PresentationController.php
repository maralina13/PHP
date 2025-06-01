<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Application;
use app\exceptions\FileException;

class PresentationController
{
    public function getView(): void
    {
        // Добавляем навигацию в шапку
        Application::$app->getRouter()->renderView("presentation");
    }

    public function handleView(): void
    {
        $body = Application::$app->getRequest()->getBody();
        $filename = PROJECT_ROOT . "runtime/" . "dump.txt";

        try {
            $f = fopen($filename, "a");
            if ($f === false) {
                throw new FileException("Cannot open file", $filename);
            }

            foreach ($body as $key => $value) {
                if (fwrite($f, "$key=>$value" . PHP_EOL) === false) {
                    throw new FileException("Cannot write to file", $filename);
                }
            }

            fclose($f);

            // Перенаправляем после успешной обработки
            Application::$app->getResponse()->redirect('/home');

        } catch (\Exception $e) {
            if (isset($f) && is_resource($f)) {
                fclose($f);
            }
            throw $e;
        }
    }
}