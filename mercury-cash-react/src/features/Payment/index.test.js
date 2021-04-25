import { Payment } from "./index";
import { customRender } from "../../utils/testUtils";
import { initialState, store } from "../../state/state";
import { renderHook, act } from '@testing-library/react-hooks'

const transactionData = {
  cryptoAmount: 0.04230942,
  confirmations: 3,
  address: '0x521273d0a5b93fbb4001c40b12d88e632ebeee8b',
  qrCodeText: 'SomeQRCode',
  exchangeRate: '1,182.77',
  networkFee: '0.000102891241761',
  uuid: 'someUUID',
  cryptoCurrency: 'ETH'
}

describe('<Payment/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Render Complete Component', async () => {
    let writeTransactionData, state;
    renderHook(() => ({
      writeTransactionData, state
    } = store()))
    expect(state).toBe(initialState)

    act(() => {
      writeTransactionData(transactionData)
    })

    const { container } = customRender(<Payment/>)
    expect(container).toMatchSnapshot()
    expect(spy).not.toHaveBeenCalled()
  })
})