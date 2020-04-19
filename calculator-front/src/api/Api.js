export default class Api {
    constructor() {
        this._baseUrl = '/api/controller.php';
    }

    async _getResource(opts) {
        const res = await fetch(this._baseUrl, opts);

        return res.json();
    }

    async initAction() {
        const opts = {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'action=init'
        };

        return this._getResource(opts);
    }

    updateAction() {

    }

    //todo remove this test action once it will ok
    async testAction() {
        const res = await fetch(this._baseUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `action=test`
        });

        return res.json();
    }
}