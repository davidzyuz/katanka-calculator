export default class FetchApi {
  constructor() {
    this._baseUrl = '/controller.php';
    this._data = null;
  }

  async getResource(opts) {
    const res = await fetch(this._baseUrl, opts);

    return res.json();
  }

  async sendDatePickerAction(date) {
    return this.getResource({
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: `action=datepicker&date=${date}`
    });
  }
}
