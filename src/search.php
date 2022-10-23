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
    $errors['prefectures'] = '都道府県を入力してください';
}

if (!$municipalities) {
    $errors['municipalities'] = '市区町村を入力してください';
}

if (!$street) {
    $errors['street'] = '町名を入力してください';
}

if (count($errors) === 0) {
    $address = [
        'prefectures' => $prefectures,
        'municipalities' => $municipalities,
        'street' => $street,
        'extendAddress' => $extendAddress
    ];

    $getHazard = new GetHazard($address);
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
    $categoryScore = [];
    for ($i = 0; $i < count($scores); $i++) {
        $categoryScore[] = $categories[$i] . ':' . (string)$scores[$i];
    }
    $chartItem = array_map(function($category) {
        return '"' . $category . '"';
    }, $categoryScore);

    $title = '解析結果';
    $content = __DIR__ . '/views/result.php';
    include __DIR__ . '/views/layout.php';
} else {
    $title = 'エリアチェッカー';
    $content = __DIR__ . '/views/home.php';
    include __DIR__ . '/views/layout.php';
}
