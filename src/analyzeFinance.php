<?php
// パラメータ
require_once __DIR__ . '/getStatistics.php';

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

        $areaCode = $this->getArea();
        $statisticData = [];

        foreach ($categories as $category) {
            $info = $this->getInfo($this->appId, $this->statisticsId, $areaCode, $category);

            $categoryName = explode('（', $info['name'])[0];
            $statisticData[$info['year']][$categoryName] = $info['value'] . $info['unit'];
        }
        return $statisticData;
    }

    public function evaluate(): array
    {
        $result['score'] = 0;
        $result['message'] = [];
        $statisticData = $this->getStatisticData();
        foreach ($statisticData as $arr) {
            if ((int)$arr['実質公債費比率'] < 25) {
                $result['score'] += 0.5;
            } elseif ((int)$arr['実質公債費比率'] < 35) {
                $result['score'] += 0.25;
                $result['message'][] = '実質公債費比率が早期健全化基準を超えています。';
            } else {
                $result['score'] += 0;
                $result['message'][] = '実質公債費比率が財政再生基準を超えています。';
            }

            if ((int)$arr['将来負担比率'] < 350) {
                $result['score'] += 0.5;
            } else {
                $result['score'] += 0;
                $result['message'][] = '将来負担比率が早期健全化基準を超えています。';
            }

            if ((int)$arr['実質赤字比率'] < 11.25) {
                $result['score'] += 0.5;
            } elseif ((int)$arr['実質赤字比率'] < 20) {
                $result['score'] += 0.25;
                $result['message'][] = '実質赤字比率が早期健全化基準を超えています。';
            } else {
                $result['score'] += 0;
                $result['message'][] = '実質赤字比率が財政再生基準を超えています。';
            }

            if ((int)$arr['連結実質赤字比率'] < 16.25) {
                $result['score'] += 0.5;
            } elseif ((int)$arr['連結実質赤字比率'] < 30) {
                $result['score'] += 0.25;
                $result['message'][] = '連結実質赤字比率が早期健全化基準を超えています。';
            } else {
                $result['score'] += 0;
                $result['message'][] = '連結実質赤字比率が財政再生基準を超えています。';
            }
        }

        $result['statisticData'] = $statisticData;
        $result['category'] = '財政';
        if (!count($result['message'])) {
            $result['message'][] = '健全化判断比率に該当する指標はありません。';
        }

        return $result;
    }
}
