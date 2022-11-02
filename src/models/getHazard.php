<?php

require_once(__DIR__ . '/../lib/readEnv.php');

class GetHazard
{
    private string $apiKey;
    private string $area;

    public function __construct(private array $address)
    {
        $this->area = $this->address['prefectures'] . $this->address['municipalities'] . $this->address['street'] . $this->address['extendAddress'];
        $this->apiKey = readEnv()[5];
    }

    public function getGeocoding(): array
    {
        $geocodeApiUrl = "https://maps.googleapis.com/maps/api/geocode/json?key=" . $this->apiKey . '&address=' . urlencode($this->area);

        $geocodeJson = file_get_contents($geocodeApiUrl);
        $geocodeData = json_decode($geocodeJson, true);

        if ($geocodeData['status'] === "ZERO_RESULTS") {
            $errors['result'] = '該当する住所が存在しないため、解析を終了します。';
            $prefectures = $this->address['prefectures'];
            $municipalities = $this->address['municipalities'];
            $street = $this->address['street'];
            $extendAddress = $this->address['extendAddress'];
            $title = 'エリアチェッカー';
            $content = __DIR__ . '/views/home.php';
            include __DIR__ . '/views/layout.php';
            exit;
        }

        $lon = $geocodeData["results"][0]["geometry"]["location"]["lng"];
        $lat = $geocodeData["results"][0]["geometry"]["location"]["lat"];

        return [
            'lon' => $lon,
            'lat' => $lat
        ];
    }

    public function getMaxDepth(): float|int|null
    {
        $geocoding = $this->getGeocoding();
        $query = http_build_query($geocoding, '', '&', PHP_QUERY_RFC3986);
        $url = 'https://suiboumap.gsi.go.jp/shinsuimap/Api/Public/GetMaxDepth?' . $query;

        $json = file_get_contents($url);
        $maxDepthInfo = json_decode($json, true);
        if ($maxDepthInfo === null){
            return null;
        }
        return $maxDepthInfo['Depth'];
    }

    public function getBreakPoint(): array
    {
        $geocoding = $this->getGeocoding();
        $query = http_build_query($geocoding, '', '&', PHP_QUERY_RFC3986);
        $url = 'https://suiboumap.gsi.go.jp/shinsuimap/Api/Public/GetBreakPoint?' . $query . '&returnparams=EntryRiverName';

        $json = file_get_contents($url);
        $arr = json_decode($json, true);
        $breakPoint = array_unique(array_column($arr, 'EntryRiverName'));
        return $breakPoint;
    }

    public function evaluate(): array
    {
        $maxDepth = $this->getMaxDepth();
        $result = [];
        $result['statisticsData']['maxDepth'] = $maxDepth;
        $result['category'] = '災害';

        if ($maxDepth === null) {
            $result['score'] = 2;
            $result['message'][] = 'この地域は浸水が想定されていない区域であるため、水害の可能性は低いです。まだシミュレーションデータが登録されていないだけの可能性もあるため、詳細は自治体のハザードマップをご確認ください。';
        } else {
            $breakPoint = $this->getBreakPoint();
            $result['statisticsData']['breakPoint'] = $breakPoint;
            $result['score'] = 1;
            $result['message'][] = 'この地域は浸水想定区域です。この地点の洪水時の想定最大侵水深は' . $maxDepth . 'mです。破堤点となる可能性のある川は' . implode('、', $breakPoint) . 'です。';
        }
        return $result;
    }
    //sample
    // https://suiboumap.gsi.go.jp/shinsuimap/Api/Public/GetMaxDepth?lon=132.825909&lat=35.41577515775&grouptype=0
    // →float(1.279)
}
