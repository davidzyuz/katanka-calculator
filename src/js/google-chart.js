google.charts.load('current', {'packages':['corechart']});
google.charts.setOnLoadCallback(drawChart);

let chartData;

function setChartData(data) {
  chartData = data;
}

function makeRequest() {

  function stateHandler() {
    if (xhttp.readyState === 4 && xhttp.status === 200) {
      const { response } = xhttp;
      setChartData(JSON.parse(response));
    }
  }

  const xhttp = new XMLHttpRequest();

  xhttp.onreadystatechange = stateHandler;
  xhttp.open('POST', '/controller.php', true);
  xhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhttp.setRequestHeader('Cache-Control', 'no-cache');
  xhttp.send('action=fetch_chart_data');
}

makeRequest();

function drawChart() {
  const data = new google.visualization.DataTable();

  data.addColumn('string', 'День');
  data.addColumn('number', 'Цена');
  data.addColumn({type: 'string', role: 'tooltip'});

  data.addRows(chartData.map((el) => {
    const formattedPrice = Number(el.price).toLocaleString().replace(',', ' '),
          formattedCashless = Number(el.cashless).toLocaleString().replace(',', ' ');

    const tooltipText = `Дата: ${el.date} 
                         Цена: ${formattedPrice} 
                         -10%: ${formattedCashless}`;

    return [el.date, el.price, tooltipText];
  }));

  const options = {
    title: 'Динамика цены',
    curveType: 'function',
    legend: {
      position: 'bottom'
    },
    tooltip: {
      isHtml: true
    }
  };

  const chart = new google.visualization.LineChart(document.getElementById('chart_div'));

  chart.draw(data, options);
}
