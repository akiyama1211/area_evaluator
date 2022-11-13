<?php
// パラメータ
require_once __DIR__ . '/../lib/readEnv.php';

abstract class GetStatistics
{
    protected string $statisticsId;
    protected string $appId;
    protected array $metaData;
    protected string $timeCode;
    protected string $area;

    public function __construct(protected array $address)
    {
        $this->area = $this->address['prefectures'] . ' ' .  $this->address['municipalities'];
        $this->appId = readEnv()[4];
        $this->metaData = $this->getMetaData();
        $this->timeCode = $this->getTime();
    }

    public function getMetaData(): array
    {
        $params = array(
            'appId' => $this->appId,
            'statsDataId' => $this->statisticsId,
        );

        // URLエンコード
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);

        // 統計表のメタ情報を取得（Json形式）
        $url = 'http://api.e-stat.go.jp/rest/2.0/app/json/getMetaInfo?' . $query;
        $json = file_get_contents($url);

        // Json形式を配列に変換
        $metaData = json_decode($json, true);
        return $metaData;
    }

    public function getTime(): string
    {
        // 時間データを取得
        $timeKeys = array_keys($this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS']);

        $maxTimeKey = $timeKeys[count($timeKeys) - 1];
        $timeCode = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][3]['CLASS'][$maxTimeKey]['@code'];
        return $timeCode;
    }

    public function getArea(): string
    {
        // エリアデータを取得
        $areaMetaData = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][2]['CLASS'];

        $areaKey = array_search($this->area, array_column($areaMetaData, '@name'));

        if (!$areaKey) {
            $errors['result'] = '該当する住所が存在しないため、解析を終了します。';
            $prefectures = $this->address['prefectures'];
            $municipalities = $this->address['municipalities'];
            $street = $this->address['street'];
            $extendAddress = $this->address['extendAddress'];
            $title = 'エリアチェッカー';
            $content = __DIR__ . '/../views/home.php';
            include __DIR__ . '/../views/layout.php';
            exit;
        }

        $areaCode = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][2]['CLASS'][$areaKey]['@code'];
        return $areaCode;
    }

    public function execQuery(string $appId, string $statisticsId, string $areaCode, string $category): array
    {
        $params = array(
            'appId' => $appId,
            'statsDataId' => $statisticsId,
            'cdTime' => $this->timeCode,
            'cdArea' => $areaCode,
            'cdCat01' => $category
        );
        $query = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        // 統計データを取得
        $url = 'http://api.e-stat.go.jp/rest/2.0/app/json/getStatsData?' . $query;
        $json = file_get_contents($url);
        $arr = json_decode($json, true);
        return $arr;
    }

    public function exitData(array $arr): bool
    {
        return key_exists('DATA_INF', $arr['GET_STATS_DATA']['STATISTICAL_DATA']);
    }

    public function processInfo(array $arr): array
    {
        $name = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['CLASS_INF']['CLASS_OBJ'][1]['CLASS']['@name'];
        $categoryName = explode('_', $name)[1];
        $value = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['$'];
        $unit = $arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['@unit'];
        $year = (int)substr($arr['GET_STATS_DATA']['STATISTICAL_DATA']['DATA_INF']['VALUE']['@time'], 0, 4);
        $info = [
            'name' => $categoryName,
            'value' => $value,
            'unit' => $unit,
            'year' => $year
        ];
        return $info;
    }

    public function getInfo(string $appId, string $statisticsId, string $areaCode, string $category): array
    {
        $arr = $this->execQuery($appId, $statisticsId, $areaCode, $category);
        $result = $this->exitData($arr);
        while (!$result) {
            $this->timeCode = (string)((int)$this->timeCode - 1000000);
            $arr = $this->execQuery($this->appId, $this->statisticsId, $areaCode, $category);
            $result = $this->exitData($arr);
        }
        $info = $this->processInfo($arr);
        return $info;
    }

    abstract public function getStatisticData();
    abstract public function evaluate();
}
