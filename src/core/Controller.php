<?php

class Controller
{
    protected $actionName;
    protected $request;

    public function __construct($application)
    {
        $this->request = $application->request;
    }

    public function run($action): mixed
    {
        $this->actionName = $action;

        if (!method_exists($this, $action)) {
            throw new HttpNotFoundException();
        }
        $content = $this->$action();
        return $content;
    }

    protected function render($variables = [], $template = null, $layout = 'layout'): string|false
    {
        $view = new View(__DIR__ . '/../views');

        if (is_null($template)) {
            $template = $this->actionName;
        }

        $controllerName = strtolower(substr(get_class($this), 0, -10));
        $path = $controllerName . '/' . $template;
        return $view->render($path, $variables, $layout);
    }
}
