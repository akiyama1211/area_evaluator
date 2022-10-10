<!-- <h3><?php echo $fullAddress?>の解析結果</h3> -->
<div>
  <canvas id="myChart"></canvas>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.5.1/chart.js"></script>
  <script>
    var ctx = document.getElementById("myChart");
    var myChart = new Chart(ctx, {
      type: 'radar',
      data: {
        labels: ["国語", "算数", "理科", "社会",],
        datasets: [{
          label: "前期試験成績",
          data: [2, 0.4, 0.5, 0.4],
          backgroundColor: "rgba(67, 133, 215, 0.5)",  //グラフ背景色
          borderColor: "rgba(67, 133, 215, 1)",        //グラフボーダー色
        }]
      },
      options: {
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
