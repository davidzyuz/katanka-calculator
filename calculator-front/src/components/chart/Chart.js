import React, { useEffect } from "react";
import ChartJs from "chart.js";

import "./chart.css";

/*
const testData = [
  168068,
  169042,
  169419,
  170088,
  169992,
  170253,
  170897,
  171087,
  171400,
  171749,
  171174,
  170585,
  170780,
  171145,
  171313,
  171512,
  171478,
  171303,
  171054,
  170569
];
*/

//available keys: date, price, cashless
function formatToValues(data, key) {
  return data.reduce((acc, curr) => {
    acc.push(curr[key]);
    return acc;
  }, []);
}

export default function(props) {
  if (!props.chartData.length) {
    const { chartEvent } = props;
    chartEvent();
    return <h3>loading...</h3>;
  }
  const ctx = React.createRef(),
    prices = formatToValues(props.chartData, 'price'),
    dates = formatToValues(props.chartData, 'date'),
    cashless = formatToValues(props.chartData, 'cashless');


  // Отрисовка графика
  useEffect(() => {
    const chart = new ChartJs(ctx.current, {
      type: 'line',
      data: {
        labels: dates,
        datasets: [{
          label: 'Динамика Цены', // заглавие (в самом верху графика
          data: prices, // данные для графика
          borderWidth: 1,
          borderColor: [
            'rgba(15, 76, 129, 1)'
          ],
          pointBackgroundColor: 'rgba(15, 76, 129, 1)',
          pointRadius: 5
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { //ticks. Используются для кастомизации осей.
          xAxes: [{
            type: 'category',
            ticks: {
              autoSkip: false,
              maxRotation: 45,
              minRotation: 45
            }
          }]
        },
        elements: {
          line: {
            tension: 0
          }
        },
      }
    });
  }, [ctx]);

  return (
    <div className="chart-container">
      <canvas ref={ctx} id="katanka-chart" width="400" height="400" role="img"/>
    </div>
  );
}
