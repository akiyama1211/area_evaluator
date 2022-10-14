<?php

require_once(__DIR__ . '/getHazard.php');
require_once(__DIR__ . '/analyzeFinance.php');
require_once(__DIR__ . '/getHospital.php');
require_once(__DIR__ . '/getDemographics.php');

$prefectures = htmlspecialchars($_POST['prefectures'], ENT_QUOTES, 'UTF-8');
$municipalities = htmlspecialchars($_POST['municipalities'], ENT_QUOTES, 'UTF-8');
$street = htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8');
$extendAddress = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');

$errors = [];

if (!$prefectures) {
    $errors[] = 'noPrefectures';
}

if (!$municipalities) {
    $errors[] = 'noMunicipalities';
}

if (!$street) {
    $errors[] = 'noStreet';
}

if (count($errors) === 0) {
    $time_start = microtime(true);
    $fullAddress = $prefectures . $municipalities . $street . $extendAddress;
    $address = $prefectures . ' ' . $municipalities;

    $getHazard = new GetHazard($fullAddress);
    $analyzeFinance = new AnalyzeFinance($address);
    $getDemographics = new GetDemographics($address);
    $getHospital = new GetHospital($address);

    $analyzers = [$getHazard, $analyzeFinance, $getDemographics, $getHospital];

    $results = [];
    foreach ($analyzers as $analyzer) {
        $results[] = $analyzer->evaluate();
    }
    $scores = array_column($results, 'score');
    $categories = array_column($results, 'category');
    $chartItem = array_map(function($category) {
        return '"' . $category . '"';
    }, $categories);

    $time = microtime(true) - $time_start;
    echo "{$time} 秒";
    $title = '解析結果';
    $content = __DIR__ . '/views/result.php';
    include __DIR__ . '/views/layout.php';
} else {
    $errorString = '';
    for ($i = 0; $i < count($errors); $i++) {
        if ($i <> 0) {
            $errorString = $errorString . '&';
        }
        $errorString = $errorString . 'error[' . $i . ']=' . $errors[$i];
    }
    header('Location: home.php?' . $errorString);
}
