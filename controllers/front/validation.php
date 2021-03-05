<?php
/**
* 2007-2020 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class MercuryCashValidationModuleFrontController extends ModuleFrontController
{


    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
        $log = dirname(__FILE__) . '/debug.log';

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
        Context::getContext()->cart = new Cart((int) $cart_id);
        Context::getContext()->customer = new Customer((int) $customer_id);
        Context::getContext()->currency = new Currency((int) Context::getContext()->cart->id_currency);
        Context::getContext()->language = new Language((int) Context::getContext()->customer->id_lang);

        //get cart currency
        $currency = new Currency($currency_id);
        $currency_iso =  $currency->iso_code;

        //get available currencies
        $module_currencies = $this->module->getAvailableCurrencies();
        //if order currency is not equal to one of the module currency we stop process
        if ($module_currencies) {
            $module_currencies_array = array_keys($module_currencies);
            if (!in_array($currency_iso, $module_currencies_array)) {
                die(Tools::jsonEncode(['result'=> false, 'error' => 'Order currency not allowd.']));
            }
        }

        //get cart parameters
        $amount = $this->context->cart->getOrderTotal();
        $crypto_type = Tools::getValue('currency_option');
        $secure_key = Context::getContext()->customer->secure_key;
        $available_crypto_types = ['BTC', 'ETH', 'DASH'];

        //check crypto type
        if (!in_array($crypto_type, $available_crypto_types)) {
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Wrong crypto currency type.']));
        }

        //check minimum amount for choosen crypto type
        $minimum_amount = $this->module->getMinimumAmount($crypto_type);
        if ($amount < $minimum_amount) {
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Minimum amount with '.$crypto_type.': '.$minimum_amount.'.']));
        }

        //get module api keys
        $api_key = $this->module->getApiKey();
        if (!$api_key) {
            die($this->module->l('Wrong API keys'));
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
            error_log(PHP_EOL . 'exception I: '. $error . PHP_EOL, 3, $log);
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            error_log(PHP_EOL . 'exception II: '. $error . PHP_EOL, 3, $log);
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
        } catch (Exception $e) {
            error_log(PHP_EOL . 'exception III: '. $e->getMessage() . PHP_EOL, 3, $log);
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
        }

        //get transaction uid;
        $uuid = $transaction->getUuid();

         //checkout transaction
        try {
            $checkout = $endpoint->process($uuid);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            error_log(PHP_EOL . 'exception IV: '. $error . PHP_EOL, 3, $log);
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            $response = $e->getResponse();
            $error = $response->getBody()->getContents();
            error_log(PHP_EOL . 'exception V: '. $error . PHP_EOL, 3, $log);
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
        } catch (Exception $e) {
            error_log(PHP_EOL . 'exception VI: '. $e->getMessage() . PHP_EOL, 3, $log);
            die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
        }

        //get status of transaction
         try {
             $status = $endpoint->status($uuid);
         } catch (\GuzzleHttp\Exception\ClientException $e) {
             $response = $e->getResponse();
             $error = $response->getBody()->getContents();
             error_log(PHP_EOL . 'exception VII: '. $error . PHP_EOL, 3, $log);
             die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
         } catch (\GuzzleHttp\Exception\ServerException $e) {
             $response = $e->getResponse();
             $error = $response->getBody()->getContents();
             error_log(PHP_EOL . 'exception VIII: '. $error . PHP_EOL, 3, $log);
             die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
         } catch (Exception $e) {
             error_log(PHP_EOL . 'exception IX: '. $e->getMessage() . PHP_EOL, 3, $log);
             die(Tools::jsonEncode(['result'=> false, 'error' => 'Mercury error. Please, try later.']));
         }

        $address = $transaction->getAddress();
        $crypto_amount = $transaction->getCryptoAmount();

        //get qr-code address
        $qr = "bitcoin:$address?amount=$crypto_amount&cryptoCurrency=$crypto_type";

        die(Tools::jsonEncode([
            'result'=> true,
            'uuid' => $uuid,
            'address' => $address,
            'qr' => $qr,
            'cart_id' => $cart_id,
            'secure_key' => $secure_key,
            'crypto_amount' => $crypto_amount,
            'crypto_type' => $crypto_type,
            'amount' => $amount,
            'current_currency' => $currency_iso,
            'network_coast' => $transaction->getFee(),
            'exchange_rate' => $transaction->getRate(),
            'total' => $crypto_amount
        ]));
    }

    protected function isValidOrder()
    {
        /*
         * Add your checks right there
         */
        return true;
    }

}
