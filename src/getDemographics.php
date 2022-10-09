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

    public function getTime(): array
    {
        // 時間データを取得
        $timeKeys = array_keys($this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS']);

        $fiveYearsKey = array_slice($timeKeys, count($timeKeys) - 3);

        $timeCodes = [];
        foreach ($fiveYearsKey as $yearKey) {
            $timeCodes[] = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS'][$yearKey]['@code'];
        }
        return $timeCodes;
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

        $timeCodes = $this->getTime();
        $areaCode = $this->getArea();

        // URLエンコード
        $statisticData = [];
        foreach ($timeCodes as $timeCode) {
            foreach ($categories as $category) {
                $params = array(
                    'appId' => $this->appId,
                    'statsDataId' => $this->statisticsId,
                    'cdTime' => $timeCode,
                    'cdArea' => $areaCode,
                    'cdCat01' => $category
                );
                $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
                // 統計データを取得
                $url = 'http://api.e-stat.go.jp/rest/2.0/app/json/getStatsData?' . $query;
                $json = file_get_contents($url);

                $arr = json_decode($json, true);
                $year = substr($timeCode, 0, 4);
                $name = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['CLASS_INF']['CLASS_OBJ'][1]['CLASS']['@name'];
                $categoryName = explode('_', $name)[1];
                $value = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['$'];
                $unit = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['@unit'];
                $statisticData[(int)$year][$categoryName] = $value . $unit;
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
