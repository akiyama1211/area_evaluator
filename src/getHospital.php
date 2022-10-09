<?php
// パラメータ
require_once __DIR__ . '/getStatistics.php';
require_once __DIR__ . '/lib/readEnv.php';
require_once __DIR__ . '/lib/sql.php';

class GetHospital extends GetStatistics
{
    const STATISTICS_ID = '0000020309';

    public function __construct($area)
    {
        $this->statisticsId = self::STATISTICS_ID;
        parent::__construct($area);
    }

    public function getTime(): string
    {
        // 時間データを取得
        $timeKeys = array_keys($this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS']);

        $maxTimeKey = $timeKeys[count($timeKeys) - 1];
        $timeCode = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS'][$maxTimeKey]['@code'];
        return $timeCode;
    }

    public function getStatisticData(): array
    {
        $categories = [
            '#I0950102',
            '#I0950103'
        ];

        #I0950102_一般病院数（可住地面積100km2当たり）
#I0950103_一般診療所数（可住地面積100km2当たり）

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
            $categoryName = explode('_', $name)[1];
            $value = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['$'];
            $unit = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['@unit'];
            $lastYear = (int)substr($timeCode, 0, 4);
            $statisticData[$lastYear][$categoryName]['area'] = $value . $unit;

            $sql = "SELECT average, median FROM hospital WHERE category_id = '{$category}'";
            $recordValue = getData($sql);
            $statisticData[$lastYear][$categoryName]['average'] = $recordValue[0]['average'] . $unit;
            $statisticData[$lastYear][$categoryName]['median'] = $recordValue[0]['median'] . $unit;
        }
        return $statisticData;
    }

    public function infoResult(): void
    {
        $statisticData = $this->getStatisticData();
        foreach ($statisticData as $year => $value) {
            foreach ($value as $categoryName => $index) {
                echo $year . '年の' . $categoryName . 'は' . $index['area'] . 'です。' . PHP_EOL;
                echo '全国平均値は' . $index['average'] . '、全国中央値は' . $index['median'] . 'です。' . PHP_EOL;
            }
        }
    }

    public function createAverage(array $areaMetaData): void
    {
        $wardsOfTokyo = [
            '千代田区' => '13101',
            '中央区' => '13102',
            '港区' => '13103',
            '新宿区' => '13104',
            '文京区' => '13105',
            '台東区' => '13106',
            '墨田区' => '13107',
            '江東区' => '13108',
            '品川区' => '13109',
            '目黒区' => '13110',
            '大田区' => '13111',
            '世田谷区' => '13112',
            '渋谷区' => '13113',
            '中野区' =>'13114',
            '杉並区' => '13115',
            '豊島区' => '13116',
            '北区' => '13117',
            '荒川区' => '13118',
            '板橋区' => '13119',
            '練馬区' => '13120',
            '足立区' => '13121',
            '葛飾区' => '13122',
            '江戸川区' => '13123'
        ];
        $areaCodes = [];
        foreach ($areaMetaData as $value) {
            if (($value['@level'] === '2' && $value['@code'] <> '13100') || in_array($value['@code'], $wardsOfTokyo)) {
                $areaCodes[$value['@name']] = $value['@code'];
            }
        }
        $categories = [
            '#I0950102',
            '#I0950103'
        ];
        $timeCode = $this->getTime();
        $lastYear = (int)substr($timeCode, 0, 4);

        foreach ($categories as $category) {
        $sql = "SELECT year FROM hospital WHERE category_id = '{$category}'";
        $recordYear = (int)getData($sql)[0]['year'];

        if ($lastYear > $recordYear) {

            $sql = 'DELETE FROM hospital WHERE category_id = (?)';
            $deleteValue = [];
            $deleteValue[] = $category;
            deleteData($sql, $deleteValue);

                $statisticData = [];
                foreach ($areaCodes as $areaCode) {
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
                    $value = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['$'];
                    $statisticData[] = (float)$value;
                }
                sort($statisticData);
                $average = array_sum($statisticData)/count($statisticData);
                if (count($statisticData) % 2 === 0) {
                    $median = ($statisticData[(count($statisticData) / 2) - 1] + $statisticData[(count($statisticData) / 2)]) / 2;
                } else {
                    $median = $statisticData[floor(count($statisticData)) / 2];
                }
                if ($category === '#I0950102') {
                    $name = 'hospital per 100km2';
                } else {
                    $name = 'clinic per 100km2';
                }

                $values  = [
                    $lastYear,
                    $average,
                    $median,
                    $name,
                    $category
                ];

                $sql = 'INSERT INTO hospital ( year, average, median, category_name, category_id) VALUES (' . implode(',', array_fill(0, count($values), '?')) . ')';
                createData($sql, $values);
            }
        }
    }
}
