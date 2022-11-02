<?php

require_once(__DIR__ . '/../lib/sql.php');
require_once(__DIR__ . '/../core/Controller.php');

class HomeController extends Controller
{
    public function index()
    {
        $errors = [];

        $prefectures = '';
        $municipalities = '';
        $street = '';
        $extendAddress = '';
        $title = 'TOWN SELECT';
        $content = __DIR__ . '/../views/home.php';

        include __DIR__ . '/../views/layout.php';
    }

    public function explain()
    {
        $title = 'TOWN SELECT 概要';
        $content = __DIR__ . '/../views/explain.php';

        include __DIR__ . '/../views/layout.php';
    }

    public function inquiry()
    {
        $title = 'TOWN SELECT お問い合わせ';
        $content = __DIR__ . '/../views/inquiry.php';

        include __DIR__ . '/../views/layout.php';
    }
}
