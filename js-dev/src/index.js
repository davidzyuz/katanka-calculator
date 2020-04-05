import { makeRequest } from "./data-request";
import { requestChartData } from "./google-chart";
import $ from "jquery";
import Materialize from "materialize-css";
import Fetch from "./fetch-api";
import './main.css';


$(document).ready(function () {
  makeRequest();
  setTimeout(() => requestChartData(), 500);
  const dtpick = document.querySelector('.datepicker');

  Materialize.Datepicker.init(dtpick, {
    onSelect: () => console.log('selected'),
    onDraw: () => console.log('drawed'),
    onClose: () => console.log('closed')
  });

  $('#foo-btn').click(function () {
    fetch('/controller.php')
      .then(res => res.json())
      .then(data => console.log(data));
  });
});
