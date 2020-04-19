import React from "react";

function CurrentValue(props) {
    return <p>
        Значение меди по {props.name}: {props.value}.
        Данные актуальны на {props.actualDate}
    </p>
}

export default function Title() {
    //todo api call function
    return <CurrentValue
        name="LME"
        value="1232,4"
        actualDate="04-04-2020"
    />
}
