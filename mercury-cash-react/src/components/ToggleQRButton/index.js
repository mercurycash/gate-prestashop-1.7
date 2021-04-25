import React from 'react';
import PropTypes from "prop-types";
import './styles.scss'

export const ToggleQRButton = ({ text, clickHandler = () => {} }) => {
  return (
    <button onClick={clickHandler} className="mercury-cash__btn-reset mercury-cash__toggleQRButton">
      { text }
    </button>
  );
};

ToggleQRButton.propTypes = {
  text: PropTypes.string,
  clickHandler: PropTypes.func
}