import React, {useState, useEffect, Fragment} from 'react';
import Header from "../header";
import PriceInfo from "../price-info";
import Datepicker from "../datepicker";
import Chart from "../chart";
import Api from "../../api";
import {registerLocale, setDefaultLocale} from "react-datepicker";
import ru from "date-fns/locale/ru";

import "./app.css";

registerLocale('ru', ru);
setDefaultLocale('ru');

const STATUS_STORED = 1;
const FORMULA_VALUE_UPDATED = 1;
const FORMULA_VALUE_COMMON = 2;

export default function () {
  const api = new Api(),
    [action, setAction] = useState('init'),
    [isUpdatedFormula, setIsUpdatedFormula] = useState(FORMULA_VALUE_COMMON),
    [initData, setInitData] = useState({}),
    [updateData, setUpdateData] = useState({}),
    [chartData, setChartData] = useState(null),
    [reInitData, setReInitData] = useState(false),
    [datepickerData, setDatepickerData] = useState({});

  /* TODO: сдeлaть так, чтобы update ничего не возвращал, (кроме успешного сообщения), а после update -> отправлять еще
   * один запрос на init, который вернёт уже новые данные.
   **/
  // Available actions: init, update, chart_data, datepicker, test


  useEffect(() => {
    api.performAction('init')
      .then(data => {
        const { first_var: firstVar, second_var: secondVar } = data;
        setInitData({firstVar, secondVar, ...data});
        })
      .catch(error => console.error(error));

    setIsUpdatedFormula(FORMULA_VALUE_COMMON);
  }, [isUpdatedFormula]);

  /**
   * Fires when chart data should be updated.
   */
  function chartEvent() {
    setAction('chart_data');
  }

  /**
   * Event that fires on prize change
   * @param action
   * @param params object
   */
  function formulaValueChangeEvent(action, params) {
    api.performAction(action, params)
      .then(data => {
        if (data.status === STATUS_STORED) {
          setIsUpdatedFormula(FORMULA_VALUE_UPDATED);
        }
      })
      .catch(error => console.error(error));
  }

  /**
   * Event that fires on date change. Fetches data for specific date, recalculate data for chart too.
   * @param action
   * @param params
   */
  function datepickerChangeEvent(params) {
    api.performAction('datepicker', params)
      .then(data => {
        setInitData(prevData => {
          const { prize, firstVar, secondVar } = prevData,
            { bnValue: bn, cashValue: cash } = data,
            lmeAverage = data.lmeAverage.value,
            minfinAverage = data.minfinAverage.value,
            currentLme = data.lme.value,
            storedLmeDate = data.lme.stored_at,
            currentMinfin = data.minfin.value,
            storedMinfinDate = data.minfin.stored_at;

          return { prize, firstVar, secondVar, bn, cash, lmeAverage, minfinAverage, currentLme, storedLmeDate, currentMinfin, storedMinfinDate };
        });
      })
      .catch(error => console.error(error));

    api.performAction('chart_data', params)
      .then(data => {
        setChartData([...data]);
      })
      .catch(error => console.error(error));
  }

  const chartResultedData = chartData === null ? initData['chartData'] : chartData;

  return (
    <Fragment>
      <Header {...initData} />
      <PriceInfo {...initData} formulaValueChangeEvent={formulaValueChangeEvent}/>
      <Datepicker datepickerChangeEvent={datepickerChangeEvent}/>
      <Chart chartEvent={chartEvent} chartData={chartResultedData}/>
    </Fragment>
  );
}
