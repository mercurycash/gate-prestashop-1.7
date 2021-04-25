/* eslint-disable react-hooks/rules-of-hooks */
import { useState } from 'react';

import cryptocurrencies from "../configs/cryptocurrencies";


export const initialState = {
	step: 1,
	createTransactionStatus: false,
	cryptoButtons: cryptocurrencies
}

export const store = () => {
	const [ state, setState ] = useState(initialState)

	// Write initialization to state
	const initData = (data) => setState(prevState => ({ ...prevState, initializedData: data }))

	// Change modal steps
	const changeStep = (step) => setState(prevState => ({ ...prevState, step: step }))

	// Transaction status
	const createTransactionStatus = (bool) => setState(prevState => ({
		...prevState,
		createTransactionStatus: bool
	}))

	// Write checkout data in state from checkoutQuery
	const writeTransactionData = (obj) => setState(prevState => ({ ...prevState, transactionData: obj }))

	// write transaction status in transactionData
	const writeTransactionStatus = (status) => setState(prevState => ({
		...prevState,
		transactionData: { ...prevState?.transactionData, status: status }
	}))
	
	// Set confirmation
	const setConfirmation = (confirmations) => setState(prevState => ({
		...prevState,
		transactionData: { ...prevState?.transactionData, confirmations: confirmations }
	}))

	// Update confirmations status
	const updateStatusConfirmation = (data) => setState(prevState => ({
		...prevState, transactionData: {
			...prevState.transactionData,
			confirmations: data
		}
	}))
  
  // error
  const errorMessage = (data) => setState(prevState => ({
    ...prevState,
    error: data
  }))

	return {
		state,
		changeStep,
		createTransactionStatus,
		initData,
		writeTransactionData,
		updateStatusConfirmation,
		writeTransactionStatus,
		setConfirmation,
    errorMessage
	}
}