import React, { useState } from 'react';
import Header from "../header/Header";
import Api from "../../api/Api";

export default function App() {
    const api = new Api(),
        [apiData, setApiData] = useState({
            prize: null,
            firstVar: null,
            secondVar: null,
            bn: null,
            cash: null,
            lmeAverage: null,
            minfinAverage: null,
            currentLme: null,
            currentMinfin: null,
            storedLmeDate: null,
            storedMinfinDate: null
        });

    api.initAction()
        .then(data => {
            console.log(data);
            setApiData({
            prize: data.prize,
            firstVar: data.first_var,
            secondVar: data.second_var,
            bn: null,
            cash: null,
            lmeAverage: null,
            minfinAverage: null,
            currentLme: null,
            currentMinfin: null,
            storedLmeDate: null,
            storedMinfinDate: null
            });
        })
        .catch(error => console.error(error));

    return (
        <div>
            <Header />
        </div>
    )
}
