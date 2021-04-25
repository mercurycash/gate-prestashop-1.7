import axios from "axios";
import qs from 'qs';

export const checkoutQuery = (name, state) => {
	const data = {
		price: state?.initializedData?.price,
		crypto: name,
		currency: state?.initializedData?.currency,
		email: state?.initializedData?.email
	}

	// eslint-disable-next-line react-hooks/rules-of-hooks
	return axios({
		method: 'POST',
		headers: { 'content-type': 'application/x-www-form-urlencoded' },
		data: qs.stringify(data),
		url: state?.initializedData?.checkoutUrl,
	})
}

export const checkPayedStatus = (state) => {
	const data = {
		uuid: state?.transactionData?.uuid
	}
	
	return axios({
		method: 'POST',
		headers: { 'content-type': 'application/x-www-form-urlencoded' },
		data: qs.stringify(data),
		url: state?.initializedData?.statusUrl,
	})
}