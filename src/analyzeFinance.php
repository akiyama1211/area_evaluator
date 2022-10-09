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

    public function getTime(): string
    {
        // 時間データを取得
        $timeKeys = array_keys($this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS']);

        $maxTimeKey = array_keys($timeKeys, max($timeKeys))[0];

        unset($timeKeys[$maxTimeKey]);

        $secondaryTimeKey = max($timeKeys);

        $timeCode = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS'][$secondaryTimeKey]['@code'];
        return $timeCode;
    }

    public function getStatisticData(): array
    {
        $categories = [
            'D2203',
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

        $timeCode = $this->getTime();
        $areaCode = $this->getArea();

        // URLエンコード
        $statisticData = [];
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
            $name = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['CLASS_INF']['CLASS_OBJ'][1]['CLASS']['@name'];
            $categoryName = explode('（', explode('_', $name)[1])[0];
            $value = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['$'];
            $unit = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['@unit'];
            $statisticData[$categoryName] = $value . $unit;
        }
        return $statisticData;
    }

    public function infoResult(): void
    {
        $statisticData = $this->getStatisticData();
        foreach ($statisticData as $categoryName => $index) {
            echo $categoryName . 'は' . $index . 'です。' . PHP_EOL;
        }
    }
}
