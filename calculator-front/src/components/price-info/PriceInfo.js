import React, {useState, useEffect} from "react";
import Popup from "../popup/Popup";
import { usePopper } from "react-popper";

import "./price-info.css";

const PRIZE = 1;
const FIRST_VAR = 2;
const SECOND_VAR = 3;
const DEFAULT_FIRST_VAR = 10;

// Премия
function Prize({prize, formulaValueChangeEvent}) {
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
    const params = {'value': prizeVal, 'value_to_change': PRIZE};
    formulaValueChangeEvent('update', params);
  }

  return (<input
    type="number"
    name="prize"
    value={prizeVal}
    onChange={changeHandler}
    onBlur={blurHandler}
    onSubmit={(e) => e.preventDefault()}/>);
}

// Price subtract percent
function PriceWithDiscount({firstVar, formulaValueChangeEvent, bn, inputName}) {
  const [percent, setPercent] = useState(0),
    [prevVal, setPrevVal] = useState(null);

  if (prevVal !== firstVar) {
    setPrevVal(firstVar);
    setPercent(firstVar);
  }

  function blurHandler(e) {
    const params = {'value': percent, 'value_to_change': FIRST_VAR};
    formulaValueChangeEvent('update', params);
  }

  function changeHandler(e) {
    const value = Number(e.target.value);
    setPercent(value.toString());
  }

  return (
    <div>
      {formatPrice(bn)}
      <span className="sign">-</span>
      <input
        type="number"
        name={inputName}
        value={percent}
        onChange={changeHandler}
        onBlur={blurHandler}
      />
      <span className="sign">%</span>
      <span className="sign">=</span>
      {formatPrice(bn * ((100 - percent) / 100))}
    </div>
  )
}

function AverageValue(props) {
  const { id, clickHandler } = props;
  return <div id={props.id} onClick={props.clickHandler}>{props.average}</div>;
}

function formatPrice(num) {
  return Math.round(num).toLocaleString('ru-RU', {style: 'decimal'});
}

/**
 * Main price component
 *
 * @param props
 * @returns {*}
 * @constructor
 */
function MainPrice (props) {
  const [referenceElement, setReferenceElement] = useState(null),
    [popperElement, setPopperElement] = useState(null),
    [arrowElement, setArrowElement] = useState(null),
    instance = usePopper(referenceElement, popperElement, {
    modifiers: [{ name: 'arrow', options: { element: arrowElement } }],
  });
  // TODO: about update https://popper.js.org/docs/v2/constructors/#types
  const show = () => popperElement.setAttribute('data-show', '');

  const clickHandler = (e) => {
    setReferenceElement(e.target);
    show();
  }

  const hide = () => popperElement.removeAttribute('data-show');

  const popupOnClick = (e) => {
    hide();
  };

  return (
    <div id="main-price-container">
      <span className="sign">(</span>
      <AverageValue id="lme-average" average={props.lmeAverage} clickHandler={clickHandler}/>
      <span className="sign">+</span>
      <Prize prize={props.prize} formulaValueChangeEvent={props.formulaValueChangeEvent} />)
      <span className="sign">)</span>
      <span className="sign">x</span>
      <AverageValue id="minfin-average" average={props.minfinAverage} clickHandler={clickHandler}/>
      <span className="sign">x</span>
      1,2
      <span className="sign">=</span>
      {formatPrice(props.bn)}
    </div>
  );
}

export default function (props) {
  const { bn, cash, firstVar, formulaValueChangeEvent } = props;
  return (
    <div id="price">
      <div id="price-title">
        <h1>Цена</h1>
      </div>
      <div id="price-container">
        <MainPrice {...props} />
        <PriceWithDiscount bn={bn} cash={cash} inputName="firstVar" firstVar={firstVar} formulaValueChangeEvent={formulaValueChangeEvent}/>
        <PriceWithDiscount bn={bn} cash={cash} inputName="firstVar" firstVar={DEFAULT_FIRST_VAR} formulaValueChangeEvent={formulaValueChangeEvent}/>
      </div>
    </div>
  );
}
