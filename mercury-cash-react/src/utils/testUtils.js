import { render } from "@testing-library/react";
import { Provider } from "outstated";
import { store } from "../state/state";

export const customRender = (ui) => {
  return render(
    <Provider stores={ [ store ] }>{ ui }</Provider>
  )
}
