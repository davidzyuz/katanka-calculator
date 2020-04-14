export default class HelpersFunctions {
  get nodePrize() {
    return [...document.querySelectorAll("input[name='prize']")];
  }

  get bnValue() {
    return [...document.querySelectorAll('.bn-value')];
  }

  get cashValue() {
    return document.querySelector('.cash-value');
  }

  get averLme() {
    return document.querySelector('.average-lme');
  }

  get averMinfin() {
    return document.querySelector('.average-minfin');
  }

  get currLme() {
    return document.querySelector('.current-lme');
  }

  get currMinfin() {
    return document.querySelector('.current-minfin');
  }

  get lmeDate() {
    return document.querySelector('.stored-lme-date');
  }

  get minfinDate() {
    return document.querySelector('.stored-minfin-date');
  }

  render(data) {
    this.bnValue
      .map(el => el.innerHTML = Math.round(data.bnValue)
        .toLocaleString()
        .replace(',', ' '));

    this.cashValue.innerHTML = Math.round(data.cashValue)
      .toLocaleString()
      .replace(',', ' ');

    this.averLme.innerHTML = data.lmeAverage.value;
    this.averMinfin.innerHTML = data.minfinAverage.value;
    this.currLme.innerHTML = data.lme.value;
    this.currMinfin.innerHTML = data.minfin.value;
    this.lmeDate.innerHTML = data.lme['stored_at'];
    this.minfinDate.innerHTML = data.minfin['stored_at'];
  }
}
