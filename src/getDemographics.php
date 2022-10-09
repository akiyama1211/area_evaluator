<?php
// パラメータ
require_once __DIR__ . '/getStatistics.php';
require_once __DIR__ . '/lib/readEnv.php';

class GetDemographics extends GetStatistics
{
    const STATISTICS_ID = '0000020201';

    public function __construct($area)
    {
        $this->statisticsId = self::STATISTICS_ID;
        parent::__construct($area);
    }

    public function getStatisticData(): array
    {
        $categories = [
            'A4101',
            'A4200',
            'A5103',
            'A5104'
        ];
        // 'A4101'出生数,
        // 'A4200',死亡数
        // 'A5103',転入者数
        // 'A5104',転出者数

        $timeCode = $this->timeCode;
        $areaCode = $this->getArea();
        $statisticData = [];

        foreach ($categories as $category) {
            $info = $this->getInfo($this->appId, $this->statisticsId, $timeCode, $areaCode, $category);

            $statisticData[$info['year']][$info['name']] = $info['value'] . $info['unit'];
        }

        $timeCodes = [
            (string)((int)$timeCode - 1000000),
            (string)((int)$timeCode - 2000000)
        ];

        foreach ($timeCodes as $timeCode) {
            foreach ($categories as $category) {
                $arr = $this->execQuery($this->appId, $this->statisticsId, $timeCode, $areaCode, $category);
                $info = $this->processInfo($arr);
                $statisticData[$info['year']][$info['name']] = $info['value'] . $info['unit'];
            }
        }
        return $statisticData;
    }

    public function infoResult(): void
    {
        $statisticData = $this->getStatisticData();
        foreach ($statisticData as $year => $value) {
            foreach ($value as $categoryName => $index)
            echo $year . '年の' . $categoryName . 'は' . $index . 'です。' . PHP_EOL;
        }
    }
}
