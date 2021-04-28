<?php

class MercuryCashValidationModuleFrontController extends ModuleFrontController
{

    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
        /*
         * If the module is not active anymore, no need to process anything.
         */
        if ($this->module->active == false) {
            die;
        }

        /**
         * Since it is an example, we choose sample data,
         * You'll have to get the correct values :)
         */
        $order_cart = $this->context->cart;
        $cart_id = $order_cart->id;
        $customer_id = $order_cart->id_customer;
        $currency_id = $this->context->cart->id_currency;

        /*
         * Restore the context from the $cart_id & the $customer_id to process the validation properly.
         */
        $context = Context::getContext();
        $context->cart = new Cart((int) $cart_id);
        $context->customer = new Customer((int) $customer_id);
        $context->currency = new Currency((int) $context->cart->id_currency);
        $context->language = new Language((int) $context->customer->id_lang);

        //get cart currency
        $currency = new Currency($currency_id);
        $currency_iso =  $currency->iso_code;

        //get available currencies
        $module_currencies = $this->module->getAvailableCurrencies();
        //if order currency is not equal to one of the module currency we stop process
        if ($module_currencies) {
            $module_currencies_array = array_keys($module_currencies);
            if (!in_array($currency_iso, $module_currencies_array)) {
                die(Tools::jsonEncode(['data' => ['error' => 'Order currency not allowed.']]));
            }
        }

        //get cart parameters
        $amount = $this->context->cart->getOrderTotal();
        $crypto_type = Tools::getValue('crypto');
        $secure_key = $context->customer->secure_key;
        $this->context->cookie->__set('secure_key', $secure_key);
        $available_crypto_types = ['BTC', 'ETH', 'DASH'];

        //check crypto type
        if (!in_array($crypto_type, $available_crypto_types)) {
            die(Tools::jsonEncode(['data' => ['result'=> false, 'error' => 'Wrong crypto currency type.']]));
        }

        //check minimum amount for choosen crypto type
        $minimum_amount = $this->module->getMinimumAmount($crypto_type);
        if ($amount < $minimum_amount) {
            die(Tools::jsonEncode(['data' => ['error' => 'Minimum amount with '.$crypto_type.': '.$minimum_amount.'.']]));
        }

        //get module api keys
        $api_key = $this->module->getApiKey();
        if (!$api_key) {
            die(Tools::jsonEncode(['data' => ['error' => 'Wrong API keys']]));
        }

        //create transaction
        $adapter = $this->module->isSandbox() ?
            new \MercuryCash\SDK\Adapter($api_key, 'https://api-way.mercurydev.tk') :
            new \MercuryCash\SDK\Adapter($api_key);

        $endpoint = new \MercuryCash\SDK\Endpoints\Transaction($adapter);
        try {
            $transaction = $endpoint->create([
                'crypto' => $crypto_type,
                'fiat' => $currency_iso,
                'amount' => $amount,
                'tip' => 0,
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            die(Tools::jsonEncode(['data' => ['error' => 'Mercury error. Please, try later.']]));
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            die(Tools::jsonEncode(['data' => ['error' => 'Mercury error. Please, try later.']]));
        } catch (Exception $e) {
            die(Tools::jsonEncode(['data' => ['result'=> false, 'error' => 'Mercury error. Please, try later.']]));
        }

        //get transaction uid;
        $uuid = $transaction->getUuid();

         //checkout transaction
        try {
            $checkout = $endpoint->process($uuid);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            die(Tools::jsonEncode(['data' => ['result'=> false, 'error' => 'Mercury error. Please, try later.']]));
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            die(Tools::jsonEncode(['data' => ['result'=> false, 'error' => 'Mercury error. Please, try later.']]));
        } catch (Exception $e) {
            die(Tools::jsonEncode(['data' => ['result'=> false, 'error' => 'Mercury error. Please, try later.']]));
        }

        //get status of transaction
         try {
             $status = $endpoint->status($uuid);
         } catch (\GuzzleHttp\Exception\ClientException $e) {
             $response = $e->getResponse();
             $error = $response->getBody()->getContents();
             die(Tools::jsonEncode(['data' => ['error' => 'Mercury error. Please, try later.']]));
         } catch (\GuzzleHttp\Exception\ServerException $e) {
             $response = $e->getResponse();
             $error = $response->getBody()->getContents();
             die(Tools::jsonEncode(['data' => ['error' => 'Mercury error. Please, try later.']]));
         } catch (Exception $e) {
             die(Tools::jsonEncode(['data' => ['result'=> false, 'error' => 'Mercury error. Please, try later.']]));
         }

        $address = $transaction->getAddress();
        $crypto_amount = $transaction->getCryptoAmount();

        switch ($crypto_type) {
            case 'BTC':
                $type = 'bitcoin';
                break;
            case 'ETH':
                $type = 'ethereum';
                break;
            case 'DASH':
                $type = 'dash';
        }
        //get qr-code address
        $qr = "$type:$address?amount=$crypto_amount&cryptoCurrency=$crypto_type";

        die(Tools::jsonEncode([
            'data' => [
                'cryptoAmount' => $crypto_amount,
                'confirmations' => 5,
                'address' => $address,
                'qrCodeText' => $qr,
                'exchangeRate' => $transaction->getRate(),
                'networkFee' => $transaction->getFee(),
                'uuid' => $uuid,
                'cryptoCurrency' => $crypto_type
            ]
        ]));
    }

    /**
     * @return bool
     */
    protected function isValidOrder()
    {
        /*
         * Add your checks right there
         */
        return true;
    }

}
