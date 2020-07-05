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
function PriceWithDiscount(props) {
  const [inputVal, setInputVal] = useState(0);

  if (inputVal === 0 && !isNaN(props.firstVar)) {
    setInputVal(props.firstVar);
  }

  // Have no idea how to do better
  const [percent, setPercent] = useState(0);

  function blurHandler(e) {
    setPercent(inputVal);
  }

  useEffect(() => {
    const params = {'value': percent, 'value_to_change': FIRST_VAR};
    props.formulaValueChangeEvent('update', params);
  }, [percent]);

  function changeHandler(e) {
    const value = Number(e.target.value);
    setInputVal(value.toString());
  }

  return (
    <div>
      {props.bn} -
      <input
        type="number"
        name={props.inputName}
        value={inputVal}
        onChange={changeHandler}
        onBlur={blurHandler}
      />
      = {props.bn * ((100 - percent) / 100)}
    </div>
  )
}

function AverageValue(props) {
  const { id, clickHandler } = props;
  return <div id={props.id} onClick={props.clickHandler}>({props.average}</div>;
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
    console.log(e.target);
    setReferenceElement(e.target);
    show();
  }

  const hide = () => popperElement.removeAttribute('data-show');

  const popupOnClick = (e) => {
    hide();
  };

  return (
    <div>
      <h1>Цена</h1>
      <AverageValue id="lme-average" average={props.lmeAverage} clickHandler={clickHandler}/> +
      <Prize prize={props.prize} formulaValueChangeEvent={props.formulaValueChangeEvent} />) x
      <AverageValue id="minfin-average" average={props.minfinAverage} clickHandler={clickHandler}/> x 1,2 = {props.bn}
    </div>
  );
}

export default function (props) {
  const { bn, cash, firstVar, formulaValueChangeEvent } = props;
  return (
    <div id="price-container">
      <MainPrice {...props} />
      <PriceWithDiscount bn={bn} cash={cash} inputName="firstVar" firstVar={firstVar} formulaValueChangeEvent={formulaValueChangeEvent}/>
      <PriceWithDiscount bn={bn} cash={cash} inputName="firstVar" firstVar={DEFAULT_FIRST_VAR} formulaValueChangeEvent={formulaValueChangeEvent}/>
    </div>
  );
}
