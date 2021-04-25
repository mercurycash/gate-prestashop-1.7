import { SelectCurrency } from "./index";
import { customRender } from "../../utils/testUtils";

describe('<SelectCurrency/>', () => {
  it('should render component and create snapshot', () => {
    const { container } = customRender(<SelectCurrency/>)
    expect(container).toMatchSnapshot()
  })
})