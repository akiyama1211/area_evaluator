<?php

class Router
{
    private $routes;
    public function __construct($routes)
    {
        $this->routes = $routes;
    }

    public function resolve($pathInfo): mixed
    {
        foreach ($this->routes as $path => $pattern) {
            if ($path === $pathInfo) {
                return $pattern;
            }
        }
        return false;
    }
}
