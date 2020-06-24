import React, { forwardRef } from "react";

import "./popup.css";

const Popup = forwardRef((props, ref) => {
  return <div
    ref={ref}
    className="popup"
    onClick={props.clickHandler}
  >some text here</div>;
});

export default Popup;
