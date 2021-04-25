import { ToggleQRButton } from "./index";
import { customRender } from "../../utils/testUtils";


describe('<WalletAddress/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Render ToggleQRButton Component', async () => {
    const { getByRole } = customRender(<ToggleQRButton text={ 'CLick me' }
                                                       clickHandler={ () => console.log('Changed to Wallet Address') }/>)
    expect(getByRole('button')).toBeInTheDocument()
    expect(spy).not.toHaveBeenCalled()
  })
})