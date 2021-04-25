import ReactDOM from 'react-dom';
import { useCallback, useEffect } from 'react'
import { useStore } from "outstated";
import i18n from "i18next";

import { store } from '../../state/state'
import { eventEmitter } from "../../utils/eventEmitter";
import { useTranslation } from "react-i18next";

// Assets
import './App.scss';
import { ReactComponent as CloseSvg } from '../../assets/icons/icon-close.svg'
import { ReactComponent as BackArrowSvg } from '../../assets/icons/icon-back-arrow.svg'
import { ReactComponent as Logo } from '../../assets/images/logos/logo.svg'

// Components
import { Loader } from "../../components/Loader";
import { SelectCurrency } from "../SelectCurrency";
import { Payment } from "../Payment";
import { PaymentReceived } from "../PaymentReceived";
import { Complete } from "../Complete";
import { useStep } from "../../hook/useStep";

/**
 * App
 * */
function App({ initializingData, currency, price, email }) {
  const { t } = useTranslation()
  const { state, initData } = useStore(store)
  const { isStepPayment, isStepReceived, firstStep, isSelectCurrency, isStepComplete, goToCurrencyStep } = useStep()

  useEffect(() => {
    i18n.changeLanguage(initializingData?.lang)
    initData({
      ...initializingData,
      currency,
      price,
      email
    })
    setTimeout(() => {
      goToCurrencyStep()
    }, 1000)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  const handleClickStepBack = useCallback(() => {
    goToCurrencyStep()
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ state ])

  const handleClickCloseModal = useCallback(() => {
    // If current is Payment step need to configm for close modal
    if (isStepPayment && !window.confirm(`${ t('closeConfirm') }`)) return

    eventEmitter.emit('close', {
      ...state?.transactionData
    })
    const element = document.querySelector(state.initializedData.mount)
    ReactDOM.unmountComponentAtNode(element)
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ state ])

  const body = useCallback(() => {
    if (firstStep) return <Loader><Logo/></Loader>
    if (isSelectCurrency) return <SelectCurrency/>
    if (isStepPayment) return <Payment/>
    if (isStepReceived) return <PaymentReceived/>
    if (isStepComplete) return <Complete/>

    return new Error('Some Error please write TS')
  }, [ firstStep, isSelectCurrency, isStepComplete, isStepPayment, isStepReceived ])

  return (
    <div className="mercury-cash-wrapper">
      <div className="mercury-cash-container">
        <div className="mercury-cash-content">
          <div className="mercury-cash">
            <div className="mercury-cash__body">
              { body() }
            </div>

            {
              !isStepReceived &&
              <button onClick={ handleClickCloseModal }
                      className="mercury-cash__close mercury-cash__btn-reset mercury-cash__top-btn">
                <CloseSvg/>
              </button>
            }

            {
              isStepPayment &&
              <button onClick={ handleClickStepBack }
                      className="mercury-cash__back mercury-cash__btn-reset mercury-cash__top-btn">
                <BackArrowSvg/>
              </button>
            }
          </div>
        </div>
      </div>
    </div>
  );
}

export default App;
