import React from 'react';
import { useTranslation } from "react-i18next";
import Countdown from 'react-countdown';
import './styles.scss'

/**
 * Timer
 **/
export const Timer = ({ date }) => {
  const { t } = useTranslation()
  /**
   * Change format renderer countdown
   * */
  const renderer = ({ minutes, seconds, completed }) => {
    if (completed) {
      return '00:00';
    } else {
      return <span>{ minutes < 10 ? `0${ minutes }` : minutes }:{ seconds < 10 ? `0${ seconds }` : seconds }</span>;
    }
  };

  return (
    <div className="mercury-cash__timer">
      { t('expireTransaction') }: <Countdown date={ date } renderer={ renderer }/>
    </div>
  );
};