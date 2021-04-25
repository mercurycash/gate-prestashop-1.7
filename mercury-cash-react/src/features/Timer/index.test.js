import { customRender } from "../../utils/testUtils";
import { Timer } from "./index";


describe('<Timer/>', () => {
  it('should render component', () => {
    customRender(<Timer date={ Date.now() + 90000 }/>)
  })
})