import React, { useRef } from 'react'
import PropTypes from "prop-types";
import './styles.scss'

import { ReactComponent as CopyIcon } from '../../assets/icons/icon-copy.svg'

export const WalletAddress = ({ walletAddress = '' }) => {
  const wallerAddressRef = useRef()

  const copyClickHandler = () => {
    const text = wallerAddressRef?.current?.innerText;
    navigator?.clipboard?.writeText(text)
  }

  return (
    <div className="mercury-cash__wallet-address">
      <span ref={ wallerAddressRef }>
        { walletAddress }
      </span>
      <button onClick={ () => copyClickHandler() } data-testid={ 'wallet-button' } className="mercury-cash__btn-reset">
        <CopyIcon/>
      </button>
    </div>
  );
};

WalletAddress.propTypes = {
  walletAddress: PropTypes.string.isRequired
}