import { TRANSACTION_RECEIVED } from "../configs/statuses";
import { useEffect, useRef, useState } from "react";
import { useStore } from "outstated";
import { store } from "../state/state";
import { checkPayedStatus } from "../services/api";

export const useCheckPaymentStatus = () => {
  const { state, writeTransactionStatus, setConfirmation, errorMessage } = useStore(store)
  let timer = useRef(null)
  const [ done, setDone ] = useState(false)
  const asyncRef = useRef(true)
  const [isError, setIsError] = useState(false)

  function checkStatus() {
    return checkPayedStatus(state)
      .then((res) => {
        if (res.data.data.error) {
          setIsError(true)
          errorMessage(res.data.data.error)
          return
        }
        if (asyncRef.current) {
          setConfirmation(res.data.data.confirmations)
          if (res.data.data.status === TRANSACTION_RECEIVED) {
            writeTransactionStatus(res.data.data.status)
            setDone(true)
          }
        }
      })
      .catch((rej) => {
        new Error(rej.message)
      })
      .finally(() => {
        if (asyncRef.current) {
          // eslint-disable-next-line no-unused-vars
          timer = setTimeout(checkStatus, state.initializedData?.checkStatusInterval)
        }
      })
  }

  useEffect(() => {
    // eslint-disable-next-line react-hooks/exhaustive-deps
    timer = setTimeout(checkStatus, state.initializedData?.checkStatusInterval)

    if (process.env.REACT_APP_DEVELOPMENT === 'development') {      
      setConfirmation(1)
      writeTransactionStatus(TRANSACTION_RECEIVED)
      // Only for development
      setTimeout(() => {
        setDone(true)
      }, 3000)
    }

    return () => {
      asyncRef.current = false
      clearTimeout(timer)
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [])

  return { done, isError }
}