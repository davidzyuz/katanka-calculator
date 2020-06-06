import React from "react";

function CurrentValue(props) {
    let text;
    if (props.name === 'LME') {
        text = `Значение меди по ${props.name}: ${props.value}`;
    } else {
        text = `Текущий курс ${props.value}`;
    }
    return <p>
        {text}. Данные актуальны на {props.actualDate}
    </p>
}

export default function Header(props) {
    return (
        <div>
            <CurrentValue
                name={'LME'}
                value={props.currentLme}
                actualDate={props.storedLmeDate}
            />
            <CurrentValue
                name={'Minfin'}
                value={props.currentMinfin}
                actualDate={props.storedMinfinDate}
            />
        </div>
    )
}
