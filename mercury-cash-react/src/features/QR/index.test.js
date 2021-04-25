import { QR } from "./index";
import { customRender } from "../../utils/testUtils";

describe('<QR/>', () => {

  it('should render component', () => {
    const { container } = customRender(<QR/>)
    expect(container).toMatchSnapshot()
  })
})