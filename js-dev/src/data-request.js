import $ from 'jquery';

export function makeRequest() {
  $(document).ready(function () {
    const nodeList = document.querySelectorAll(".form-inline");
    const arrList = [...nodeList];
    const xhtp = new XMLHttpRequest();

    function render(evObj, responseData) {
      const jsonObj = JSON.parse(responseData),
        nodePrize = document.querySelectorAll("input[name='prize']"),
        arrPrize = [...nodePrize],
        bnValue = document.querySelectorAll('.bn-value'),
        cashValue = document.querySelector('.cash-value');

      arrPrize.map((el) => el.value = jsonObj.prize);

      [...bnValue].map(el => {
        el.innerHTML = Math.round(jsonObj.bn).toLocaleString().replace(',', ' ');
      });
      cashValue.innerHTML = Math.round(jsonObj.cash).toLocaleString().replace(',', ' ');
    }

    function handleResponce(e) {
      if (xhtp.readyState === 4 && xhtp.status === 200) {
        const {responseText} = xhtp;
        render(e, responseText);
      }
    }

    function focusHandler(e) {
      if (e.type === 'submit') {
        e.preventDefault();
        return false;
      }

      if (!xhtp) {
        alert('При попытке создать запрос произошла ошибка');
        return false;
      }
      const prize = encodeURIComponent(e.target.value);
      xhtp.onreadystatechange = () => handleResponce(e);
      xhtp.open('POST', '/controller.php', true);
      xhtp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhtp.setRequestHeader('Cache-Control', 'no-cache');
      xhtp.send(`action=update&prize=${prize}`);
    }

    function init() {
      function render(responce) {
        const jsonObj = JSON.parse(responce),
          nodePrize = document.querySelectorAll("input[name='prize']"),
          arrPrize = [...nodePrize],
          bnValue = document.querySelectorAll('.bn-value'),
          cashValue = document.querySelector('.cash-value'),
          averLme = document.querySelector('.average-lme'),
          averMinfin = document.querySelector('.average-minfin'),
          currMinfin = document.querySelector('.current-minfin'),
          currLme = document.querySelector('.current-lme'),
          lmeDate = document.querySelector('.stored-lme-date'),
          minfinDate = document.querySelector('.stored-minfin-date');


        arrPrize.map((el) => el.value = jsonObj.prize);

        [...bnValue].map(el => {
          el.innerHTML = Math.round(jsonObj.bn).toLocaleString().replace(',', ' ');
        });
        cashValue.innerHTML = Math.round(jsonObj.cash).toLocaleString().replace(',', ' ');
        averLme.innerHTML = new Number(jsonObj.lmeAverage).toFixed(2);
        averMinfin.innerHTML = new Intl.NumberFormat('ru-RU').format(jsonObj.minfinAverage);
        currMinfin.innerHTML = new Intl.NumberFormat('ru-RU').format(jsonObj.currentMinfin);
        currLme.innerHTML = new Intl.NumberFormat('ru-RU').format(jsonObj.currentLme);
        lmeDate.innerHTML = jsonObj.storedLmeDate;
        minfinDate.innerHTML = jsonObj.storedMinfinDate;
      }

      function handleResponce() {
        if (xhtp.readyState === 4 && xhtp.status === 200) {
          const { responseText } = xhtp;

          render(responseText);
        }
      }

      if (!xhtp) {
        alert('При попытке создать запрос произошла ошибка');
        return false;
      }
      xhtp.onreadystatechange = handleResponce;
      xhtp.open('POST', '/controller.php', true);
      xhtp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhtp.setRequestHeader('Cache-Control', 'no-cache');
      xhtp.send();
    }

    arrList.map((el) => el.addEventListener('focusout', focusHandler));
    arrList.map((el) => el.addEventListener('submit', focusHandler));

    init();
  });
}
