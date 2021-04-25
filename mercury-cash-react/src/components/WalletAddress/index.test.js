import { WalletAddress } from "./index";
import { customRender } from "../../utils/testUtils";
import fireEvent from "@testing-library/user-event";

describe('<WalletAddress/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Render WalletAddress Component', () => {
    const { getByText } = customRender(<WalletAddress walletAddress={ 'Some Wallet address' }/>)
    expect(getByText(/Some Wallet address/i)).toBeInTheDocument()
    expect(spy).not.toHaveBeenCalled()
  })

  it('Should click copy', () => {
    const { getByTestId } = customRender(<WalletAddress walletAddress={ 'Some Wallet address' }/>)
    fireEvent.click(getByTestId('wallet-button'))
  })
})