import { Title } from "./index";
import { customRender } from "../../utils/testUtils";


describe('<Title/>', () => {
  const spy = jest.spyOn(global.console, "error")

  it('Render Title Component', async () => {
    const { getByText } = customRender(<Title>Some Title</Title>)
    expect(getByText(/Some Title/i)).toBeInTheDocument()
    expect(spy).not.toHaveBeenCalled()
  })
})