import { store } from "../state/state";
import { useStore } from "outstated";

const FIRST_STEP = 1
const SELECT_CURRENCY_STEP = 2
const PAYMENT_STEP = 3
const RECEIVED_STEP = 4
const COMPLETE_STEP = 5

export const useStep = () => {
  const { state, changeStep } = useStore(store)
  const firstStep = state.step === FIRST_STEP
  const isSelectCurrency = state.step === SELECT_CURRENCY_STEP
  const isStepPayment = state.step === PAYMENT_STEP
  const isStepReceived = state.step === RECEIVED_STEP
  const isStepComplete = state.step === COMPLETE_STEP

  const goToCurrencyStep = () => changeStep(SELECT_CURRENCY_STEP)
  const goToPaymentStep = () => changeStep(PAYMENT_STEP)
  const goToReceivedStep = () => changeStep(RECEIVED_STEP)
  const goToCompleteStep = () => changeStep(COMPLETE_STEP)

  return {
    firstStep,
    isStepReceived,
    isStepPayment,
    isSelectCurrency,
    isStepComplete,
    goToPaymentStep,
    goToCurrencyStep,
    goToReceivedStep,
    goToCompleteStep
  }
}