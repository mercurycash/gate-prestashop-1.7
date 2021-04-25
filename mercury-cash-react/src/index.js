import 'react-app-polyfill/ie11';
import React, { Suspense } from 'react';
import ReactDOM from 'react-dom';
import { Provider } from 'outstated';

import './index.scss';
import App from './features/App/App';

import { store } from './state/state'
import { eventEmitter } from "./utils/eventEmitter";

window.MercurySDK = class MercurySDK {
  constructor(initData = {}) {
    this.#initializingData = {
      ...this.#initializingData,
      ...initData
    }
  }

  #initializingData = {
    checkoutUrl: '/',
    statusUrl: '/',
    checkStatusInterval: 7000,
    mount: '#mercury-cash',
    lang: 'en',
    limits: {
      BTC: 100,
      ETH: 50,
      DASH: 150
    }
  }

  checkout(price, currency = '', email = '') {
    const element = document.querySelector(this.#initializingData.mount)
    ReactDOM.unmountComponentAtNode(element)
    ReactDOM.render(
      <React.StrictMode>
        <Provider stores={ [ store ] }>
          <Suspense fallback={ null }>
            <App initializingData={ this.#initializingData }
                 price={ price }
                 currency={ currency }
                 email={ email }/>
          </Suspense>
        </Provider>
      </React.StrictMode>,
      element
    );
  }

  on(type, handler) {
    eventEmitter.on(type, handler)
  }
}