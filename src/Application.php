<?php

require_once __DIR__ . '/core/Router.php';
require_once __DIR__ . '/core/HttpNotFoundException.php';
require_once __DIR__ . '/controller/HomeController.php';
require_once __DIR__ . '/controller/AnalyzeController.php';

class Application
{
    public function __construct()
    {
        $this->router = new Router($this->registerRoutes());
    }

    public function run()
    {
        try {
            $params = $this->router->resolve($this->getPathInfo());
            if (!$params) {
                throw new HttpNotFoundException();
            }
            $controller = $params['controller'];
            $action = $params['action'];
            $this->runAction($controller, $action);
        } catch (HttpNotFoundException) {
            $this->render404Page();
        }
    }

    private function runAction($controllerName, $action)
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';
        if (!class_exists($controllerClass)) {
            throw new HttpNotFoundException;
        }
        $controller = new $controllerClass;
        $controller->run($action);
    }

    private function registerRoutes()
    {
        return [
            '/' => ['controller' => 'home', 'action' => 'index'],
            '/explain' => ['controller' => 'home', 'action' => 'explain'],
            '/inquiry' => ['controller' => 'home', 'action' => 'inquiry'],
            '/result' => ['controller' => 'analyze', 'action' => 'index'],
        ];
    }

    private function getPathInfo()
    {
        return $_SERVER['REQUEST_URI'];
    }

    private function render404Page()
    {
        header('HTTP/1.1 404 Page Not Found');
        $content = <<<EOT
        <!DOCTYPE html>
        <html>
            <head>
                <meta charset="utf-8">
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>404 </title>
            </head>
            <body>
                <h1>404 Page Not Found.</h1>
            </body>
        </html>
    EOT;
        echo $content;
    }
}
