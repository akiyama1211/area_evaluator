<?php

class Controller
{
    public function run($action)
    {
        $this->$action();
    }
}
