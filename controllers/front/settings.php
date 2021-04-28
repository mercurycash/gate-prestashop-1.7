<?php

class MercuryCashSettingsModuleFrontController extends ModuleFrontController
{

    /**
     * @throws \Exception
     */
    public function postProcess()
    {
        /*
         * If the module is not active anymore, no need to process anything.
         */
        if ($this->module->active == false) {
            die(Tools::jsonEncode(['error' => 'Module not active']));
        }
        $currency_id = $this->context->cart->id_currency;
        //get cart currency
        $currency = new Currency($currency_id);
        $currency_iso =  $currency->iso_code;
        $amount = $this->context->cart->getOrderTotal();

        $minimum_btc  = $this->module->getMinimumAmount('BTC');
        $minimum_eth  = $this->module->getMinimumAmount('ETH');
        $minimum_dash = $this->module->getMinimumAmount('DASH');

        $email = $this->context->customer->email;

        die(Tools::jsonEncode([
            'price'        => $amount,
            'currency'     => $currency_iso,
            'minimum_btc'  => $minimum_btc,
            'minimum_eth'  => $minimum_eth,
            'minimum_dash' => $minimum_dash,
            'email'        => $email
        ]));
    }

}