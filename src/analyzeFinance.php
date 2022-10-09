<?php
// パラメータ
require_once __DIR__ . '/getStatistics.php';
require_once __DIR__ . '/lib/readEnv.php';

class AnalyzeFinance extends GetStatistics
{
    const STATISTICS_ID = '0000020204';

    public function __construct($area)
    {
        $this->statisticsId = self::STATISTICS_ID;
        parent::__construct($area);
    }

    public function getStatisticData(): array
    {
        $categories = [
            'D2211',
            'D2212',
            'D2214',
            'D2215'
        ];
        // cdCat01='D2203'_経常収支比率
        // cdCat01='D2211'_実質公債費比率
        // cdCat01='D2212'_将来負担比率
        // cdCat01='D2214'_実質赤字比率
        // cdCat01='D2215'_連結実質赤字比率

        $timeCode = $this->timeCode;
        $areaCode = $this->getArea();
        $statisticData = [];

        foreach ($categories as $category) {
            $info = $this->getInfo($this->appId, $this->statisticsId, $timeCode, $areaCode, $category);

            $categoryName = explode('（', $info['name'])[0];
            $statisticData[$info['year']][$categoryName] = $info['value'] . $info['unit'];
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
