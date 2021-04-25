import { PaymentReceived } from "./index";
import { customRender } from "../../utils/testUtils";
import { initialState, store } from "../../state/state";
import { renderHook, act } from '@testing-library/react-hooks'

describe('<PaymentReceived/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Payment component render and set checkout data in state', () => {
    let writeTransactionData, state;
    renderHook(() => ({
      writeTransactionData, state
    } = store()))
    expect(state).toBe(initialState)

    act(() => {
      writeTransactionData({
        cryptoAmount: 0.04230942,
        confirmations: 3,
        address: '0x521273d0a5b93fbb4001c40b12d88e632ebeee8b',
        qrCodeText: 'SomeQRCode',
        exchangeRate: '1,182.77',
        networkFee: '0.000102891241761',
        uuid: 'someUUID',
        cryptoCurrency: 'ETH'
      })
    })

    const { container } = customRender(<PaymentReceived/>)
    expect(container).toMatchSnapshot()
  })
})