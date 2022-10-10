<?php

require_once(__DIR__ . '/lib/readEnv.php');

class GetHazard
{
    // const MAX_LON = 153.986667;
    // const MIN_LON = 122.9325;
    // const MAX_LAT = 45.557228;
    // const MIN_LAT = 20.425246;
    private string $apiKey;

    public function __construct(private string $address)
    {
        $this->apiKey = readEnv()[5];
    }

    public function getGeocoding(): array
    {
        $geocodeApiUrl = "https://maps.googleapis.com/maps/api/geocode/json?key=" . $this->apiKey . '&address=' . urlencode($this->address);

      //Geocoding APIにリクエスト
    // $context = stream_context_create(array(
    //     'http' => array('ignore_errors' => true)
    // ));
    // $geocodeJson = file_get_contents($geocodeApiUrl, false, $context);

        $geocodeJson = file_get_contents($geocodeApiUrl);

        $geocodeData = json_decode($geocodeJson, true);

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

        // if ($geocoding['lon'] < self::MIN_LON or self::MAX_LON < $geocoding['lon']) {
        //     echo '日本の緯度を入力してください' . PHP_EOL;
        //     exit;
        // }

        // if ($geocoding['lat'] < self::MIN_LAT or self::MAX_LAT < $geocoding['lat']) {
        //     echo '日本の経度を入力してください' . PHP_EOL;
        //     exit;
        // }

        $query = http_build_query($geocoding, '', '&', PHP_QUERY_RFC3986);
        $getMaxDepthUrl = 'https://suiboumap.gsi.go.jp/shinsuimap/Api/Public/GetMaxDepth?' . $query;

        $maxDepthInfo = file_get_contents($getMaxDepthUrl);
        if ($maxDepthInfo <> 'null') {
            $maxDepthString = explode('"', $maxDepthInfo);
            $maxDepth = (float)substr($maxDepthString[2], 1, strlen($maxDepthString[2]) - 2);
            return $maxDepth;
        } else {
            return null;
        }
    }

    public function evaluate(): array
    {
        $maxDepth = $this->getMaxDepth();
        $result = [];
        $result['statisticsData'][] = $maxDepth;
        $result['category'] = '災害';

        if ($maxDepth === null) {
            $result['score'] = 2;
            $result['message'][] = 'この地域では、まだシミュレーションデータが登録されていないか、浸水が想定されていない区域となります。';
        } else {
            $result['score'] = 1;
            $result['message'][] = 'この地点の洪水時の最大侵水深は' . $maxDepth . 'mです' . PHP_EOL;
        }
        return $result;
    }
    //sample
    // https://suiboumap.gsi.go.jp/shinsuimap/Api/Public/GetMaxDepth?lon=132.825909&lat=35.41577515775&grouptype=0
    // →float(1.279)
}
