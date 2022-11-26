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
