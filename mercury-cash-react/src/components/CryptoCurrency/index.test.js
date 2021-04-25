import { CryptoCurrency } from "./index";
import { customRender } from "../../utils/testUtils";
import { act } from "react-dom/test-utils";
import { screen } from '@testing-library/react'
import { renderHook, act as actHook } from '@testing-library/react-hooks'
import { initialState, store } from "../../state/state";
import userEvent from "@testing-library/user-event";

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

describe('<CryptoCurrency/>', () => {
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

  it('Render CryptoCurrency', () => {
    customRender(<CryptoCurrency name="Test name" text="Bitcoin"/>)
    expect(screen.getByText('Bitcoin')).toBeInTheDocument()
  })

  it('Click and fetch data', async () => {
    await act(async () => customRender(<CryptoCurrency name="Test name" text="My Text"/>))
    await act( async () => userEvent.click(screen.getByText('My Text')))
  })
})