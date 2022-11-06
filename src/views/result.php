<div class="container">
  <h2 class="text-center my-4">「<?php echo implode($address) ?>」の解析結果は…</h2>
  <div class="mb-5 border border-dark pr-4">
    <h1 class="text-center text-success my-4 result"><?php echo array_sum($scores)?>点です！<span class="d-inline-block">(8点満点)</span></h1>
    <div class="mb-5">
      <canvas id="myChart" class="mx-auto"></canvas>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.bundle.js"></script>
    <script>
      var ctx = document.getElementById("myChart");
      var myChart = new Chart(ctx, {
          //グラフの種類
          type: 'radar',
          //データの設定
          data: {
              //データ項目のラベル
              labels: [<? echo implode(',', $chartItem)?>],
              //データセット
              datasets: [{
                //背景色
                  backgroundColor: "rgba(67, 133, 215, 0.5)",
                  //枠線の色
                  borderColor: "rgba(67, 133, 215, 1)",
                  //結合点の背景色（ホバ時）
                  pointHoverBackgroundColor: "#fff",
                  //結合点の枠線の色（ホバー時）
                  pointHoverBorderColor: "rgba(51,255,51,1)",
                  //結合点より外でマウスホバーを認識する範囲（ピクセル単位）
                  hitRadius: 5,
                  //グラフのデータ
                  data: [<? echo implode(',', $scores)?>]
              }]
          },
          //オプションの設定
          options: {
              // レスポンシブ指定
              scale: {
                  ticks: {
                      // 最小値の値を0指定
                      beginAtZero: true,
                      min: 0,
                      stepSize: 0.5,
                      // 最大値を指定
                      max: 2,
                  },
                  pointLabels: {
                    fontSize: 20
                  }
                },
                //ラベル非表示
                legend: {
                  display: false
              }

          }
        });
    </script>
    <ul>
    <?php foreach ($results as $result):?>
        <h5><li><?php echo $result['category']?></li></h5>
        <?php foreach ($result['message'] as $message):?>
          <?php if ($result['score'] === 0):?>
            <p class="text-danger font-weight-bold"><?php echo $message?></p>
          <?php else:?>
            <p><?php echo $message?></p>
          <?php endif?>
        <?php endforeach?>
        <?php if ($result['category'] <> '災害'):?>
          <div class="table-responsive-sm">
            <table class="table table-info border-danger">
              <thead>
                <tr>
                  <th></th>
                  <?php foreach ($result['statisticData'] as $year => $arr):?>
                    <?php foreach ($arr as $category => $value):?>
                      <th><?php echo $category?></th>
                      <?php endforeach?>
                      <?php break?>
                      <?php endforeach?>
                    </tr>
                  </thead>
          <?php if ($result['category'] <> '医療'):?>
              <tbody>
                <?php foreach ($result['statisticData'] as $year => $arr):?>
                  <tr>
                    <td><?php echo $year?></td>
                    <?php foreach ($arr as $category => $value):?>
                      <td><?php echo $value?></td>
                    <?php endforeach?>
                  </tr>
                <?php endforeach?>
              </tbody>
            </table>
          </div>
        <?php else:?>
          <tbody>
              <?php foreach ($result['statisticData'] as $year => $arr):?>
                <tr>
                  <td><?php echo $address['municipalities']?><span class="d-inline-block"><?php echo '(' . $year . '年)'?></span></td>
                  <?php foreach ($arr as $category => $value):?>
                    <td><?php echo $value['area']?></td>
                  <?php endforeach?>
                </tr>
                <tr>
                  <td>全国平均値<?php echo '(' . $year . '年)'?></td>
                  <?php foreach ($arr as $category => $value):?>
                    <td><?php echo $value['average']?></td>
                  <?php endforeach?>
                </tr>
                <tr>
                  <td>全国中央値<?php echo '(' . $year . '年)'?></td>
                  <?php foreach ($arr as $category => $value):?>
                    <td><?php echo $value['median']?></td>
                  <?php endforeach?>
                </tr>
              <?php endforeach?>
            </tbody>
          </table>
        <?php endif?>
      <?php endif?>
    <?php endforeach?>
    </ul>
    <div class="width-60 border border-danger ml-4 my-4">
      <p class="h6 font-weight-bold text-danger m-3">なお、この分析は現在公開されている最新年度のデータを基に行われています。そのため、公開データが更新されていない場合、分析時点との乖離が大きくなる可能性があります。</p>
    </div>
  </div>
  <div class="text-center mb-5">
    <a href="/" class="btn btn-info mb-5 p-4 font-weight-bold h1">トップへ</a>
  </div>
</div>
