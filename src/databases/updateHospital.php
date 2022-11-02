<?php

require_once(__DIR__ . '/../models/getHospital.php');

$address = [
    'prefectures' => '東京都',
    'municipalities' => '杉並区',
    'street' => '井草',
    'extendAddress' => 'a'
];

$getHospital = new GetHospital($address);
$metaData = $getHospital->getMetaData();
$areaMetaData = $metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][2]['CLASS'];
$getHospital->updateHospital($areaMetaData);
