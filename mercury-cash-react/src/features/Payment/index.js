import React, { useState, useEffect } from 'react';
import { useTranslation } from "react-i18next";
import { useStore } from "outstated";


import { store } from '../../state/state'
import { Title } from "../../components/Title";
import { TransactionInfo } from "../../components/TransactionInfo";
import { QR } from "../QR";
import { WalletAddress } from "../../components/WalletAddress";
import { ToggleQRButton } from "../../components/ToggleQRButton";
import { Timer } from "../Timer";
import { useStep } from "../../hook/useStep";

import { useCheckPaymentStatus } from "../../hook/useCheckPaymentStatus";

/**
 * Payment
 * */
export const Payment = () => {
  const { t } = useTranslation()
  const { state } = useStore(store)
  const { goToReceivedStep } = useStep()
  const [ viewType, setViewType ] = useState(true)
  const [ date ] = useState(Date.now() + 900000)
  const { done, isError } = useCheckPaymentStatus()

  const handleClick = () => {
    setViewType(!viewType)
  }

  useEffect(() => {
    if (done) {
      goToReceivedStep()
    }
    console.log(done)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ done ])

  return (
    <>
      <Title
        large>{ t('amountBePaid') } { state.transactionData?.cryptoAmount } { state.transactionData?.cryptoCurrency }</Title>
      <TransactionInfo>
        <li>
          <strong>{ t('amountBePaid') } { state.initializedData?.currency }: </strong>
          <span>{ state.initializedData?.price } { state.initializedData?.currency }</span>
        </li>
        <li>
          <strong>{ t('exchangeRate') }: </strong>
          <span>{ state.transactionData?.exchangeRate } { state.initializedData?.currency }</span>
        </li>
        <li>
          <strong>{ t('total') }: </strong>
          <span>{ state.transactionData?.cryptoAmount } { state.transactionData?.cryptoCurrency }</span>
        </li>
      </TransactionInfo>
      <Title small lighter>{ t('address') }: </Title>

      {/* QR code or address */ }
      {
        viewType
          ? <QR value={ state.transactionData?.qrCodeText }/>
          : <WalletAddress walletAddress={ state.transactionData?.address }/>
      }

      <ToggleQRButton text={ viewType ? t('showAddress') : t('showQR') } clickHandler={ handleClick }/>
      <TransactionInfo fontSizeLargest>
        <li>
          <div><strong>{ t('networkCost') }: </strong></div>
          <div><span>{ state.transactionData?.networkFee } { state.transactionData?.cryptoCurrency }</span></div>
        </li>
      </TransactionInfo>
      <Timer date={ date }/>
      { isError && <div className="error">{ state.error }</div> }
    </>
  );
};
