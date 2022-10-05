<?php
// パラメータ
require_once __DIR__ . '/lib/readEnv.php';

abstract class GetStatistics
{
    protected string $statisticsId;
    protected string $appId;
    protected array $metaData;

    public function __construct(protected string $area)
    {
        $this->appId = readEnv()[4];
        $this->metaData = $this->getMetaData();
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

    abstract public function getTime();

    public function getArea(): string
    {
        // エリアデータを取得
        $areaMetaData = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][2]['CLASS'];

        $areaKey = array_search($this->area, array_column($areaMetaData, '@name'));

        if (!$areaKey) {
            echo '該当地域が存在しないため、処理を終了します。' . PHP_EOL;
            exit;
        }

        $areaCode = $this->metaData['GET_META_INFO']['METADATA_INF']['CLASS_INF']['CLASS_OBJ'][2]['CLASS'][$areaKey]['@code'];
        return $areaCode;
    }

    abstract public function getStatisticData();

    abstract public function infoResult();
}
