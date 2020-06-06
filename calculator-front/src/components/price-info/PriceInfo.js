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
  return (
    <div>
      <h1>Цена -10%</h1>
      {props.bn} -10% = {props.cash}
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

  return (
    <div>
      <MainPrice {...props} prizeChangeEvent={props.prizeChangeEvent} />
      <PriceWithDiscount bn={props.bn} cash={props.cash}/>
      <PriceWithDiscount bn={props.bn} cash={props.cash}/>
    </div>
  );
}
