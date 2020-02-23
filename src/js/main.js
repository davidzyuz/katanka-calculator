$(document).ready(function () {
    const nodeList = document.querySelectorAll(".form-inline");
    const arrList = [...nodeList];
    const xhtp = new XMLHttpRequest();

    function render(evObj, responseData) {
        const jsonObj = JSON.parse(responseData);
        const nodePrize = document.querySelectorAll("input[name='prize']")
        const arrPrize = [...nodePrize];
        const bnValue = document.querySelector('.bn-value');
        const cashValue = document.querySelector('.cash-value');
        arrPrize.map((el) => el.value = jsonObj.prize);
        bnValue.innerHTML = Math.round(jsonObj.bn);
        cashValue.innerHTML = Math.round(jsonObj.cash);
    }

    function handleResponce(e) {
        if (xhtp.readyState === 4 && xhtp.status === 200) {
            const { responseText } = xhtp;
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
            const jsonObj = JSON.parse(responce);
            const nodePrize = document.querySelectorAll("input[name='prize']");
            const arrPrize = [...nodePrize];
            const bnValue = document.querySelector('.bn-value');
            const cashValue = document.querySelector('.cash-value');
            arrPrize.map((el) => el.value = jsonObj.prize);
            bnValue.innerHTML = Math.round(jsonObj.bn);
            cashValue.innerHTML = Math.round(jsonObj.cash);
        }

        function handleResponce() {
            if (xhtp.readyState === 4 && xhtp.status === 200) {
                const { responseText: responseText } = xhtp;
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
