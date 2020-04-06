import { makeRequest } from "./data-request";
import { requestChartData } from "./google-chart";
import $ from "jquery";
import Materialize from "materialize-css";
import FetchApi from "./fetch-api";
import './main.css';

$(document).ready(function () {
  const myFetch = new FetchApi(),
    dtpick = document.querySelector('.datepicker');

  Materialize.Datepicker.init(dtpick, {
    onSelect: (e) => console.log(e),
    onDraw: () => console.log('drawed'),
    onClose: () => console.log('closed')
  });

  myFetch.datePickerAction().then(data => console.log(data));
  makeRequest();
  setTimeout(() => requestChartData(), 500);
});
