<?php

class AnalyzeFinance extends GetStatistics
{
    const STATISTICS_ID = '0000020204';
    const STANDARD = [
        '早期健全化基準' => [
            '実質赤字比率' => '11.25~15%',
            '連結実質赤字比率' => '16.25~20%',
            '実質公債費比率' => '25%',
            '将来負担比率' => '350%'
        ],
        '財政再生基準' => [
            '実質赤字比率' => '20%',
            '連結実質赤字比率' => '30%',
            '実質公債費比率' => '35%',
            '将来負担比率' => '-%'
        ]
    ];

    public function __construct(array $area)
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

        // cdCat01='D2211'_実質公債費比率
        // cdCat01='D2212'_将来負担比率
        // cdCat01='D2214'_実質赤字比率
        // cdCat01='D2215'_連結実質赤字比率

        $statisticData = [];

        foreach ($categories as $category) {
            $info = $this->getInfo($this->appId, $this->statisticsId, $category);

            $categoryName = explode('（', $info['name'])[0];
            $statisticData[$info['year'] . '年'][$categoryName] = $info['value'] . $info['unit'];
        }
        return $statisticData;
    }

    public function evaluate(): array
    {
        // 該当住所が見つからない時のエラー処理
        if ($this->areaCode === 'ZERO_RESULTS') {
            return ['ZERO_RESULTS'];
        }

        $result['score'] = 0;
        $result['message'] = [];
        $statisticData = $this->getStatisticData();
        foreach ($statisticData as $arr) {
            if ((int)$arr['実質公債費比率'] < 25) {
                $result['score'] += 0.5;
            } elseif ((int)$arr['実質公債費比率'] < 35) {
                $result['score'] += 0.25;
                $result['message'][] = $this->area . 'の実質公債費比率が早期健全化基準を超えています。';
            } else {
                $result['score'] += 0;
                $result['message'][] = $this->area . 'の実質公債費比率が財政再生基準を超えています。';
            }

            if ((int)$arr['将来負担比率'] < 350) {
                $result['score'] += 0.5;
            } else {
                $result['score'] += 0;
                $result['message'][] = $this->area . 'の将来負担比率が早期健全化基準を超えています。';
            }

            if ((int)$arr['実質赤字比率'] < 11.25) {
                $result['score'] += 0.5;
            } elseif ((int)$arr['実質赤字比率'] < 20) {
                $result['score'] += 0.25;
                $result['message'][] = $this->area . 'の実質赤字比率が早期健全化基準を超えています。';
            } else {
                $result['score'] += 0;
                $result['message'][] = $this->area . 'の実質赤字比率が財政再生基準を超えています。';
            }

            if ((int)$arr['連結実質赤字比率'] < 16.25) {
                $result['score'] += 0.5;
            } elseif ((int)$arr['連結実質赤字比率'] < 30) {
                $result['score'] += 0.25;
                $result['message'][] = $this->area . 'の連結実質赤字比率が早期健全化基準を超えています。';
            } else {
                $result['score'] += 0;
                $result['message'][] = $this->area . 'の連結実質赤字比率が財政再生基準を超えています。';
            }
            foreach ($arr as $category => $value) {
                $statisticData['早期健全化基準'][$category] = self::STANDARD['早期健全化基準'][$category];
                $statisticData['財政再生基準'][$category] = self::STANDARD['財政再生基準'][$category];
            }
        }

        $result['statisticData'] = $statisticData;
        $result['category'] = '財政';
        if (!count($result['message'])) {
            $result['message'][] = $this->area . 'の財政状態について、健全化判断比率に該当する指標はありません。';
        }
        return $result;
    }
}
