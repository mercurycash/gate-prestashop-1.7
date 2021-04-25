import { TransactionInfo } from "./index";
import { customRender } from "../../utils/testUtils";

const children = (
  <>
    <li>
      <strong>Amount to be Paid USD:</strong> <span>$50.00 USD</span>
    </li>
    <li>
      <strong>Exchange rate:</strong> <span>1,182.77 USD</span>
    </li>
    <li>
      <strong>Total:</strong> <span>0.04230942 ETH</span>
    </li>
  </>
)

describe('<WalletAddress/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Render Loader Component', async () => {
    const { container } = customRender(<TransactionInfo>{ children }</TransactionInfo>)
    expect(container).toMatchSnapshot()
    expect(spy).not.toHaveBeenCalled()
  })
})