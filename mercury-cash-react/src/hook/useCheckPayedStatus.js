import { useEffect, useRef, useState } from "react";
import { TRANSACTION_APROVED } from "../configs/statuses";
import { useStore } from "outstated";
import { store } from "../state/state";
import { checkPayedStatus } from "../services/api";

export const useCheckPayedStatus = () => {
	const { state, writeTransactionStatus, setConfirmation, errorMessage } = useStore(store)
	const [ doneTransaction, setDoneTransaction ] = useState(false)
	const [ confirmationDone, setConfirmationDone ] = useState(false)
	const asyncRef = useRef(true)
  const [isError, setIsError] = useState(false)

	useEffect(() => {
		let timer = setTimeout(function checkStatus() {
			checkPayedStatus(state)
				.then((res) => {
          if (res.data.data.error) {
            setIsError(true)
            errorMessage(res.data.data.error)
            return
          }
					if (asyncRef.current) {
						if (res.data.data.confirmations) {
							setConfirmation(res.data.data.confirmations)
							setConfirmationDone(true)
						}
						if (res.data.data.status === TRANSACTION_APROVED) {
							writeTransactionStatus(res.data.data.status)
							setDoneTransaction(true)
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
		}, state.initializedData?.checkStatusInterval)

		if (process.env.REACT_APP_DEVELOPMENT === 'development') {
			console.log('Only Development', process.env.REACT_APP_DEVELOPMENT)
			setConfirmation(2)
			writeTransactionStatus(TRANSACTION_APROVED)
			// Only for development
			setTimeout(() => {
				setDoneTransaction(true)
				setConfirmationDone(true)
			}, 6000)
		}

		return () => {
			asyncRef.current = false
			clearTimeout(timer)
		}

		// eslint-disable-next-line react-hooks/exhaustive-deps
	}, [])

	return {
		doneTransaction,
		confirmationDone,
    isError
	}
}


