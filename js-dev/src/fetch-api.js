export default class FetchApi {
  constructor() {
    this._baseUrl = '/controller.php';
    this._data = null;
  }

  async getResource(opts) {
    this._data = {'action': 'foo'};
    const res = await fetch(this._baseUrl, opts);

    return await res.json();
  }

  async datePickerAction() {
    return await this.getResource({
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded'
      },
      body: 'action=datepicker'
    });
  }
}
