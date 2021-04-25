import { ReactComponent as BTC } from "../assets/icons/icon-btc.svg";
import { ReactComponent as BTCHover } from "../assets/icons/icon-btc-hover.svg";
import { ReactComponent as ETH } from "../assets/icons/icon-eth.svg";
import { ReactComponent as ETHHover } from "../assets/icons/icon-eth-hover.svg";
import { ReactComponent as DASH } from "../assets/icons/icon-dash.svg";
import { ReactComponent as DASHHover } from "../assets/icons/icon-dash-hover.svg";

const cryptocurrencies = [
  {
    name: 'BTC',
    text: 'Bitcoin',
    icon: {
      default: <BTC/>,
      hover: <BTCHover/>
    }
  },
  {
    name: 'ETH',
    text: 'Ethereum',
    icon: {
      default: <ETH/>,
      hover: <ETHHover/>
    }
  },
  {
    name: 'DASH',
    text: 'Dash',
    icon: {
      default: <DASH/>,
      hover: <DASHHover/>
    }
  },
]

export default cryptocurrencies