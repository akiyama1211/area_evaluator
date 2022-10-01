<?php

require_once(__DIR__ . '/getHazard.php');
require_once(__DIR__ . '/analyzeFinance.php');

$prefectures = htmlspecialchars($_POST['prefectures'], ENT_QUOTES, 'UTF-8');
$municipalities = htmlspecialchars($_POST['municipalities'], ENT_QUOTES, 'UTF-8');
$street = htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8');
$extendAddress = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');

$errors = [];

if (!$prefectures) {
    $errors['prefectures'] = '都道府県を入力してください。';
}

if (!$municipalities) {
    $errors['municipalities'] = '市区町村を入力してください。';
}

if (!$street) {
    $errors['street'] = '町名を入力してください。';
}

// if (!$extendAddress) {
//     $errors['extendAddress'] = '番地を入力してください。';
// }
if (count($errors) === 0) {
    // $getHazard = new GetHazard();
    // $getHazard->infoResult($prefectures, $municipalities, $street, $extendAddress);

    $analyzeFinance = new AnalyzeFinance($prefectures . ' ' . $municipalities);
    $analyzeFinance->infoResult();
} else {
    header('Location: home.php');
}
