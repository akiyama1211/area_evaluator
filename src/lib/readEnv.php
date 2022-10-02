<?php

require_once __DIR__ . '/../vendor/autoload.php';

function readEnv() :array
{
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    $dbHost = $_ENV['MYSQL_HOST'];
    $dbDatabase = $_ENV['MYSQL_DATABASE'];
    $dbUsername = $_ENV['MYSQL_USER'];
    $dbPassword = $_ENV['MYSQL_PASSWORD'];
    $statisticsApi = $_ENV['STATISTICS_API'];
    $googleApi = $_ENV['GOOGLE_API'];
    return [$dbHost, $dbDatabase, $dbUsername, $dbPassword, $statisticsApi, $googleApi];
}
