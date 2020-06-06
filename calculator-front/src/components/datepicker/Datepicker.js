import React, { useState } from "react";
import DatePicker from "react-datepicker";
import { range, getDate, getYear, getMonth } from "react-datepicker";

import "react-datepicker/dist/react-datepicker.css";
import "./datepicker.css";

export default function({datepickerChangeEvent}) {
    const [date, setDate] = useState(new Date());

    const handleChange = date => {
        setDate(date);

        const formattedDate = date.toLocaleDateString('ru-RU').replace(/\./g, '-');
        const params = {'date': formattedDate};
        datepickerChangeEvent('datepicker', params);
    };

    return (
        <div>
            <DatePicker
                selected={date}
                onChange={handleChange}
                locale="ru"
                dateFormat="d MMMM yyyy"
                showMonthDropdown
                showYearDropdown
                dropdownMode="select"
            />
        </div>
    );
}
