<?php

require_once(__DIR__ . '/../lib/sql.php');

class HomeController extends Controller
{
    public function index()
    {
        return $this->render(
            [
                'errors' => [],
                'prefectures' => '',
                'municipalities' => '',
                'street' => '',
                'extendAddress' => '',
                'title' => 'トップページ',
            ]
        );
    }

    public function explain()
    {
        return $this->render(
            [
                'title' => '概要',
            ]
        );
    }

    public function inquiry()
    {
        return $this->render(
            [
                'title' => 'お問い合わせ',
            ]
        );
    }
}
