<?php

class GetDemographics extends GetStatistics
{
    const STATISTICS_ID = '0000020201';

    public function __construct(array $area)
    {
        $this->statisticsId = self::STATISTICS_ID;
        parent::__construct($area);
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

        $statisticData = [];

        foreach ($categories as $category) {
            $info = $this->getInfo($this->appId, $this->statisticsId, $category);

            $statisticData[$info['year'] . '年'][$info['name']] = $info['value'] . $info['unit'];
        }

        $timeCodes = [
            (string)((int)$this->timeCode - 1000000),
            (string)((int)$this->timeCode - 2000000)
        ];

        foreach ($timeCodes as $timeCode) {
            foreach ($categories as $category) {
                $this->timeCode = $timeCode;
                $arr = $this->execQuery($this->appId, $this->statisticsId, $category);
                $info = $this->processInfo($arr);
                $statisticData[$info['year'] . '年'][$info['name']] = $info['value'] . $info['unit'];
            }
        }
        return $statisticData;
    }

    public function evaluate(): array
    {
        if ($this->areaCode === 'ZERO_RESULTS') {
            // 該当住所が見つからない時のエラー処理
            return ['ZERO_RESULTS'];
        }

        $result = [];
        $changeValue = 0;
        $statisticData = $this->getStatisticData();
        foreach ($statisticData as $year) {
                $changeValue += (int)$year['出生数'] - (int)$year['死亡数'] + (int)$year['転入者数'] - (int)$year['転出者数'];
        }

        $result['statisticData'] = $statisticData;
        $result['category'] = '人口動態';

        if ($changeValue > 0) {
            $result['score'] = 2;
            $result['message'][] = '「' . $this->area . '」の過去３年間の人口の増減値は' . $changeValue . '人であり、増加傾向にあります。' . PHP_EOL;
        } else {
            $result['score'] = 1;
            $result['message'][] = '「' . $this->area . '」の過去３年間の人口の増減値は' . $changeValue . '人であり、減少傾向にあります。' . PHP_EOL;
        }
        return $result;
    }
}
