export default class Fetch {
  constructor() {
    this._baseUrl = '/controller.php';
  }

  async getResource() {
    const res = await fetch(this._baseUrl, {
      method: 'POST',
      headers: {
        'action': 'foo'
      }
    });
    console.log(res);
    return await res.json();
  }
}