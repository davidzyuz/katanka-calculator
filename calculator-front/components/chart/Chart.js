import React, { useEffect } from "react";
import ChartJs from "chart.js";

import "./chart.css";

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

  console.log(prices);

  // Отрисовка графика
  useEffect(() => {
    const chart = new ChartJs(ctx.current, {
      type: 'line',
      data: {
        labels: dates,
        datasets: [{
          label: '# of Votes', // заглавие (в самом верху графика
          data: prices, // данные для графика
          borderWidth: 1,
          borderColor: [
            'rgba(15, 76, 129, 1)'
          ]
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
        }
      }
    });
  }, [ctx]);

  return <canvas ref={ctx} id="katanka-chart" width="400" height="400" role="img"/>
}
