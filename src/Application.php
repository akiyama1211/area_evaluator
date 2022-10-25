<?php

require_once __DIR__ . '/core/Router.php';
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
        $params = $this->router->resolve($this->getPathInfo());
        $controller = $params['controller'];
        $action = $params['action'];
        $this->runAction($controller, $action);
    }

    private function runAction($controllerName, $action)
    {
        $controllerClass = ucfirst($controllerName) . 'Controller';
        $controller = new $controllerClass;
        $controller->run($action);
    }

    private function registerRoutes()
    {
        return [
            '/' => ['controller' => 'home', 'action' => 'index'],
            '/explain' => ['controller' => 'home', 'action' => 'explain'],
            '/result' => ['controller' => 'analyze', 'action' => 'index']
        ];
    }

    private function getPathInfo()
    {
        return $_SERVER['REQUEST_URI'];
    }
}
