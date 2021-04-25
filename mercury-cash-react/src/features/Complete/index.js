import React from 'react';
import { useTranslation } from "react-i18next";
import '../../translations/i18n'

import { Title } from "../../components/Title";

import './styles.scss'

import DoneImage from '../../assets/images/done.png'
import { ReactComponent as LogoWithText } from '../../assets/images/logos/logo-with-text.svg'

export const Complete = () => {
  const { t } = useTranslation()
  return (
    <div className="mercury-cash__complete">
      <Title large>{ t('completed') }</Title>
      <img src={ DoneImage } alt=""/>
      <Title large>{ t('thankYou') } <span>{ t('forPaying') }</span></Title>
      <LogoWithText/>
    </div>
  );
};