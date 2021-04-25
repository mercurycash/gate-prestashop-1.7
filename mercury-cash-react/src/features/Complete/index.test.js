import { Complete } from "./index";
import { customRender } from "../../utils/testUtils";

describe('<Complete/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Render Complete Component', async () => {
    const { container } = customRender(<Complete/>)
    expect(container).toMatchSnapshot()
    expect(spy).not.toHaveBeenCalled()
  })
})