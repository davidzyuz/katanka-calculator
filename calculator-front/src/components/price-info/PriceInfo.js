import React, {useState, useRef, forwardRef} from "react";
import Popup from "../popup/Popup";
import { usePopper } from "react-popper";

import "./price-info.css";

const PRIZE = 1;
const FIRST_VAR = 2;
const SECOND_VAR = 3;

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

// Цена минус процент
//Поменять процент на пропсы после возможности их обновления
function PriceWithDiscount(props) {
  const [value, setValue] = useState(0);

  if (value !== props.firstVar) {
    setValue(props.firstVar);
  }

  return (
    <div>
      {props.bn} -
      <input
        type="number"
        name={props.inputName}
        value={100 - value * 100}
        onBlur={props.firstVarChangeEvent}
      />
      = {props.cash}
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
      {/*<Popup ref={setPopperElement} clickHandler={popupOnClick} /> uncomment, when  back to popups*/}
    </div>
/*    <>
      <button type="button" ref={setReferenceElement}>
        Reference element
      </button>

      <div ref={setPopperElement} style={styles.popper} {...attributes.popper}>
        Popper element
        <div ref={setArrowElement} style={styles.arrow} />
      </div>
    </>*/
  );
}

export default function (props) {
  return (
    <div id="price-container">
      <MainPrice {...props} formulaValueChangeEvent={props.formulaValueChangeEvent} />
      <PriceWithDiscount bn={props.bn} cash={props.cash} inputName="hello" firstVar={props.firstVar}/>
      <PriceWithDiscount bn={props.bn} cash={props.cash} inputName="firstVar" firstVar={0.9}/>
    </div>
  );
}
