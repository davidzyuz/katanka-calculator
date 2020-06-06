export default class Api {
    constructor() {
        this._baseUrl = '/api/controller.php';
    }

    getDefaultFormOpts(action, params = null) {
        let paramString = ''
        if (params !== null) {
            for (const prop in params) {
                if (params.hasOwnProperty(prop)) {
                    paramString += `&${prop}=${params[prop]}`;
                }
            }
        }

        return {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=${action}${paramString}`
        }
    }

    async _getResource(opts) {
        const res = await fetch(this._baseUrl, opts);

        return res.json();
    }

    async performAction(action, params) {
        const opts = this.getDefaultFormOpts(action, params);

        return this._getResource(opts);
    }
}
