import React, { useCallback, useState } from 'react';
import { useTranslation } from "react-i18next";
import { useStore } from "outstated";

import { Title } from "../../components/Title";
import { CryptoCurrency } from "../../components/CryptoCurrency";
import { Loader } from "../../components/Loader";

import { store } from '../../state/state'

import { ReactComponent as LogoWithText } from '../../assets/images/logos/logo-with-text.svg'
import { checkoutQuery } from "../../services/api";
import { useStep } from "../../hook/useStep";

export const SelectCurrency = () => {
  const { t } = useTranslation()
  const { state, createTransactionStatus, writeTransactionData, errorMessage } = useStore(store)
  const { goToPaymentStep } = useStep()
  const [ isError, setIsError ] = useState(false)

  /**
   * Handle Click on CryptoCurrency
   * Send Post data to Checkout and write response data in store
   * */
  const onCurrencyClick = useCallback((name) => {
    createTransactionStatus(true)
    // Query for transaction data
    checkoutQuery(name, state)
      .then((resolve) => {
        if (resolve.data.data.error) {
          setIsError(true)
          errorMessage(resolve.data.data.error)
          return
        }
        if (resolve.data.data.uuid) {
          writeTransactionData(resolve.data.data)
          createTransactionStatus(false)
          goToPaymentStep()
        }
      })
      .catch((reject) => {
        new Error(reject.message)

        if (process.env.REACT_APP_DEVELOPMENT === 'development') {
          // Only for development
          setTimeout(() => {
            createTransactionStatus(false)
            goToPaymentStep()
          }, 2000)
          writeTransactionData({
            cryptoAmount: 0.04230942,
            address: '0x521273d0a5b93fbb4001c40b12d88e632ebeee8b',
            qrCodeText: 'SomeQRCode',
            exchangeRate: '1,182.77',
            networkFee: '0.000102891241761',
            uuid: 'someUUID',
            cryptoCurrency: 'ETH',
          })
        }
      })
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  function checkLimits(cryptoButton) {
    let limitPrice = state?.initializedData?.limits[cryptoButton.name]
    return limitPrice <= state?.initializedData?.price
  }

  const checkError = () => {
    return (
      isError
        ? <>
          <div className="error">{ state.error }</div>
        </>
        : <>
          <Title large>
            { t('payWithCryptoCurrency') }
          </Title>
          { state.createTransactionStatus
            ? <Loader><LogoWithText/></Loader>
            : (
              <>
                <h4 className="mercury-cash__subtitle">
                  { t('selectCrypto') }
                </h4>
                { state.cryptoButtons.map((item) => {
                  let disabled = checkLimits(item)

                  return <CryptoCurrency disabled={ !disabled } onCurrencyClick={ onCurrencyClick }
                                         key={ item.name } { ...item }/>
                }) }
              </>
            ) }
        </>
    )
  }

  return (
    <>
      { checkError() }
    </>
  );
};