import React from 'react';
import PropTypes from "prop-types";
import './styles.scss'
import classNames from "classnames";

export const TransactionInfo = ({children, fontSizeLargest}) => {
  return (
    <ul className={classNames("mercury-cash__pain-info", {'mercury-cash__pain-info--largest': fontSizeLargest})}>
      {children}
    </ul>
  );
};

TransactionInfo.propTypes = {
  fontSizeLargest: PropTypes.bool
}