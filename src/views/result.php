<h3>「<?php echo $fullAddress?>」の解析結果は…</h3>
<h3>８点中、<?php echo array_sum($scores)?>点です！</h3>
<div>
  <canvas id="myChart" width="500" height="500"></canvas>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.js"></script>
  <script>
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
      type: 'radar',
      data: {
        labels: [<? echo implode(',', $chartItem)?>],
        datasets: [{
          data: [<? echo implode(',', $scores)?>],
          backgroundColor: "rgba(67, 133, 215, 0.5)",  //グラフ背景色
          borderColor: "rgba(67, 133, 215, 1)",        //グラフボーダー色
        }]
      },
      options: {
        responsive: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          r: {
            max: 2,        //グラフの最大値
            min: 0,        //グラフの最小値
            ticks: {
              stepSize: 0.5  //目盛間隔
            }
          }
        },
      }
    });
  </script>
</div>
<ul>
    <?php foreach ($results as $result):?>
      <li><h4><?php echo $result['category']?></h4></li>
      <?php foreach ($result['message'] as $message):?>
        <p><?php echo $message?></p>
      <?php endforeach?>
    <?php endforeach?>
</ul>
