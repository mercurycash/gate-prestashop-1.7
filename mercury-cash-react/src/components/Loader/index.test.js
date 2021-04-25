import { Loader } from "./index";
import { customRender } from "../../utils/testUtils";


describe('<Loader/>', () => {

  it('Render Loader Component', async () => {
    const {getByText} = customRender(<Loader>Some Children</Loader>)
    expect(getByText(/Some Children/i)).toBeInTheDocument()
  })
})