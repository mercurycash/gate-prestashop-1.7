import React, { useState } from 'react';
import PropTypes from "prop-types";

import './styles.scss'

/**
 * CryptoCurrency
 **/
export const CryptoCurrency = (
  {
    name = '',
    text = '',
    icon = {},
    onCurrencyClick,
    disabled
  }) => {
  const [ hovered, setHovered ] = useState(false)

  return (
    <button onClick={ () => onCurrencyClick(name) }
            disabled={ disabled }
            onMouseEnter={ () => setHovered(true) }
            onMouseLeave={ () => setHovered(false) }
            className="mercury-cash__btn-reset mercury-cash__btn-crypto">
      { text }
      { hovered ? icon?.hover : icon?.default }
    </button>
  );
};

CryptoCurrency.propTypes = {
  name: PropTypes.string.isRequired,
  text: PropTypes.string.isRequired,
  icon: PropTypes.object,
  onCurrencyClick: PropTypes.func.isRequired
}

