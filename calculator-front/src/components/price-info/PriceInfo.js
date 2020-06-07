import React, {useState, useEffect} from "react";

// Премия
function Prize({prize, prizeChangeEvent}) {
  const [prizeVal, setPrizeVal] = useState(null),
    [prevPrize, setPrevPrize] = useState(null);

  if (prevPrize !== prize) {
    setPrizeVal(prize);
    setPrevPrize(prize);
  }

  function changeHandler(e) {
    const value = Number(e.target.value);
    setPrizeVal(value.toString());
  }

  function blurHandler() {
    const params = {prize: prizeVal};
    prizeChangeEvent('update', params);
  }

  return (<input
    type="number"
    name="prize"
    value={prizeVal}
    onChange={changeHandler}
    onBlur={blurHandler}
    onSubmit={(e) => e.preventDefault()}/>);
}

// Цена минус процент
//Поменять процент на пропсы после возможности их обновления
function PriceWithDiscount(props) {
  const [value, setValue] = useState(+props.firstVar * 100);
  console.log('firstvar', props.firstVar);
  return (
    <div>
      {props.bn} -
      <input type="text" name="firstVar" value={value} />
      = {props.cash}
    </div>
  )
}

// Главная цена
function MainPrice(props) {
  return (
    <div>
      <h1>Цена</h1>
      ({props.lmeAverage} +
      <Prize prize={props.prize} prizeChangeEvent={props.prizeChangeEvent}/>
      ) x {props.minfinAverage} x 1,2 = {props.bn}
    </div>
  )
}

export default function (props) {
  console.log('fsdfafasd', props);
  return (
    <div>
      <MainPrice {...props} prizeChangeEvent={props.prizeChangeEvent} />
      <PriceWithDiscount bn={props.bn} cash={props.cash} firstVar={props.firstVar}/>
      <PriceWithDiscount bn={props.bn} cash={props.cash} firstVar={10}/>
    </div>
  );
}
