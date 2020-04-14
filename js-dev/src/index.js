import HelpersFunctions from "./helpers-functions";
import { makeRequest } from "./data-request";
import { requestChartData } from "./google-chart";
import $ from "jquery";
import Materialize from "materialize-css";
import FetchApi from "./fetch-api";
import './main.css';

function onSelectHandler(e) {
  const date = new Intl.DateTimeFormat('en-GB').format(e).replace(/\//g, '-'),
    fetchApi = new FetchApi(),
    helper = new HelpersFunctions();

  fetchApi.sendDatePickerAction(date)
    .then(data => helper.render(data))
    .catch(rej => console.error(rej));
}

$(document).ready(function () {
  const dtpick = document.querySelector('.datepicker');

  Materialize.Datepicker.init(dtpick, {
    onSelect: (e) => onSelectHandler(e),
    onDraw: () => {},
    onClose: (e) => {}
  });

  makeRequest();
  setTimeout(() => requestChartData(), 500);
});
