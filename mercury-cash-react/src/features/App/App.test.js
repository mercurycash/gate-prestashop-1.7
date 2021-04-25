import { act, render, screen } from '@testing-library/react';
import { renderHook } from '@testing-library/react-hooks'

import App from './App';
import { initialState, store } from "../../state/state";
import { customRender } from "../../utils/testUtils";
import { act as actHook } from "@testing-library/react-hooks/lib/pure";

global.fetch = jest.fn(() => Promise.resolve())

const mocktransactionData = {
  cryptoAmount: 0.04230942,
  confirmations: 3,
  address: '0x521273d0a5b93fbb4001c40b12d88e632ebeee8b',
  qrCodeText: 'SomeQRCode',
  exchangeRate: '1,182.77',
  networkFee: '0.000102891241761',
  uuid: 'someUUID',
  cryptoCurrency: 'ETH'
}



describe('<App>', () => {
  beforeEach(() => {
    global.fetch = jest.fn(() => Promise.resolve({
      json: Promise.resolve(mocktransactionData)
    }))

    let initData, state;
    renderHook(() => ({
      initData, state
    } = store()))
    expect(state).toBe(initialState)

    actHook(() => {
      initData({
        checkoutUrl: '/',
        statusUrl: '/check-status',
        checkStatusInterval: 2000,
        mount: '#mercury-cash',
        price: '$200',
        currency: 'USD',
        lang: 'en'
      })
    })
  })
  
  const spy = jest.spyOn(global.console, "error")

  test('Render app component', () => {
    customRender(<App/>)
    expect(spy).not.toHaveBeenCalled()
  })

  test('Show Select Currency buttons', async () => {
    let state, changeStep;
    await act(async () => customRender(<App/>))
    renderHook(() => ({ state, changeStep } = store()))
    expect(state.step).toBe(1)
    await act(async () => changeStep(2))
    expect(state.step).toBe(2)
    expect(spy).not.toHaveBeenCalled()
  })
})
