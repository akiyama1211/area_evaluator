<?php

require_once(__DIR__ . '/../lib/sql.php');

class HomeController extends Controller
{
    public function index(): string
    {
        return $this->render(
            [
                'errors' => [],
                'prefectures' => '',
                'municipalities' => '',
                'street' => '',
                'extendAddress' => '',
                'title' => 'トップページ',
            ]
        );
    }

    public function explain(): string|false
    {
        return $this->render(
            [
                'title' => '概要',
            ]
        );
    }

    public function inquiry(): string|false
    {
        return $this->render(
            [
                'title' => 'お問い合わせ',
            ]
        );
    }

    public function analyze(): string|false
    {
        if (!$this->request->isPost()) {
            throw new HttpNotFoundException();
        }

        $prefectures = htmlspecialchars($_POST['prefectures'], ENT_QUOTES, 'UTF-8');
        $municipalities = htmlspecialchars($_POST['municipalities'], ENT_QUOTES, 'UTF-8');
        $street = htmlspecialchars($_POST['street'], ENT_QUOTES, 'UTF-8');
        $extendAddress = htmlspecialchars($_POST['address'], ENT_QUOTES, 'UTF-8');

        $errors = [];

        if (!$prefectures) {
            $errors['prefectures'] = '都道府県を入力してください';
        }

        if (!$municipalities) {
            $errors['municipalities'] = '市区町村を入力してください';
        }

        if (!$street) {
            $errors['street'] = '町名を入力してください';
        }

        if (count($errors) === 0) {
            // 必須項目が入力されている場合の処理

            $irregularMunicipalities = ['札幌市', '仙台市', 'さいたま市', '千葉市', '横浜市', '川崎市', '相模原市', '新潟市', '静岡市', '浜松市', '名古屋市', '京都市', '大阪市', '堺市', '神戸市', '岡山市', '広島市', '北九州市', '福岡市', '熊本市'];

            foreach ($irregularMunicipalities as $name) {
                if(strpos($municipalities, $name) !== false){
                    $frontMunicipalities = mb_substr($municipalities, 0, mb_strpos($municipalities, '市') + 1, 'UTF-8');
                    $behindMUnicipalities = explode('市', $municipalities)[1];

                    $municipalities = $frontMunicipalities;
                    $street = $behindMUnicipalities . $street;
                    break;
                }
            }

            $address = [
                'prefectures' => $prefectures,
                'municipalities' => $municipalities,
                'street' => $street,
                'extendAddress' => $extendAddress
            ];

            $getHazard = new GetHazard($address);
            $analyzeFinance = new AnalyzeFinance($address);
            $getDemographics = new GetDemographics($address);
            $getHospital = new GetHospital($address);

            $analyzers = [$getHazard, $analyzeFinance, $getDemographics, $getHospital];

            $results = [];
            foreach ($analyzers as $analyzer) {
                $result = $analyzer->evaluate();

                // 該当住所が見つからない時のエラー処理
                if (in_array('ZERO_RESULTS', $result)) {
                    return $this->render(
                        [
                            'errors' => ['該当する住所が存在しないため、解析を終了します。'],
                            'prefectures' => $prefectures,
                            'municipalities' => $municipalities,
                            'street' => $street,
                            'extendAddress' => $extendAddress,
                            'title' => 'トップページ',
                        ], 'index');
                }

                $results[] = $result;
            }
            $scores = array_column($results, 'score');
            $categories = array_column($results, 'category');
            $categoryScore = [];
            for ($i = 0; $i < count($scores); $i++) {
                $categoryScore[] = $categories[$i] . ':' . (string)$scores[$i];
            }
            $chartItem = array_map(function ($category) {
                return '"' . $category . '"';
            }, $categoryScore);

            return $this->render(
                [
                    'scores' => $scores,
                    'results' => $results,
                    'chartItem' => $chartItem,
                    'address' => $address,
                    'title' => '解析結果',
                ]
            );
        } else {
            // 必須項目が未入力の場合の処理

            return $this->render(
                [
                    'errors' => $errors,
                    'prefectures' => $prefectures,
                    'municipalities' => $municipalities,
                    'street' => $street,
                    'extendAddress' => $extendAddress,
                    'title' => 'トップページ',
                ], 'index');
        }
    }
}
