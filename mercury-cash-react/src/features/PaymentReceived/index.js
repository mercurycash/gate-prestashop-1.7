import React, { useEffect } from 'react';
import { useTranslation } from "react-i18next";
import { useStore } from "outstated";

import { Title } from "../../components/Title";

import { store } from "../../state/state";

import gif from '../../assets/images/preloader.gif'
import { useStep } from "../../hook/useStep";
import { useCheckPayedStatus } from "../../hook/useCheckPayedStatus";


/**
 * Payment Received
 * */
export const PaymentReceived = () => {
  const { t } = useTranslation()
  const { state } = useStore(store)
  const { goToCompleteStep } = useStep()
  const { doneTransaction, confirmationDone, isError } = useCheckPayedStatus()

  useEffect(() => {
    if (doneTransaction && confirmationDone) goToCompleteStep()

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [ doneTransaction, confirmationDone ])

  return (
    <>
      <Title large>{ t('received') }</Title>
      <h4 className="mercury-cash__text mercury-cash__text--purple">
        { t('dontCloseWindow') }
      </h4>
      <img className="mercury-cash__gif" src={ gif } alt=""/>
      <div className="mercury-cash__timer mercury-cash__timer--uppercase">
        { t('confirmations') }: { state.transactionData?.confirmations }
      </div>
      <h4 className="mercury-cash__text ">
        { t('pleaseWaitConfirmation') }
      </h4>
      { isError && <div className="error">{ state.error }</div> }
    </>
  );
};
