<?php

class AutoLoader
{
    private array $dirs;

    public function register(): void
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    public function registerDir(string $dir): void
    {
        $this->dirs[] = $dir;
    }

    private function loadClass(string $className): void
    {
        foreach ($this->dirs as $dir) {
            $file = $dir . '/' . $className . '.php';
            if (is_readable($file)) {
                require $file;
                return;
            }
        }
    }
}
