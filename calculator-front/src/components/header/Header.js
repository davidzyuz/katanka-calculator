import React from "react";

function CurrentValue(props) {
    let text;
    if (props.name === 'LME') {
        text = `${props.name} ${props.value}`;
    } else {
        text = `\$ ${props.value}`;
    }

    return <p>
        {text} на {props.actualDate.replace(/-/g, '.').slice(0, 5)}
    </p>
}

export default function Header(props) {
  if (props.storedLmeDate === undefined) {
    return <h3>Загрузка...</h3>;
  }

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
