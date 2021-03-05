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

class MercuryCashCheckModuleFrontController extends ModuleFrontController
{


    /**
     * This class should be use by your Instant Payment
     * Notification system to validate the order remotely
     */
    public function postProcess()
    {
        try {
            $uuid = Tools::getValue('uuid');
            $api_key = $this->module->getApiKey();
            $adapter = $this->module->isSandbox() ?
                new \MercuryCash\SDK\Adapter($api_key, 'https://api-way.mercurydev.tk') :
                new \MercuryCash\SDK\Adapter($api_key);
            $endpoint = new \MercuryCash\SDK\Endpoints\Transaction($adapter);
            $status_object = $endpoint->status($uuid);
            $status = $status_object->getStatus();
            if (mb_strtoupper($status) === 'TRANSACTION_RECEIVED') {
                die(Tools::jsonEncode(['result' => true, 'status' => $status]));
            }
            if (mb_strtoupper($status) === 'TRANSACTION_APROVED') {
                $cart_id = Tools::getValue('cart_id');
                $secure_key = Tools::getValue('secure_key');
                $cart = new Cart((int) $cart_id);
                $customer = new Customer((int) $cart->id_customer);
                $payment_status = Configuration::get('PS_OS_PAYMENT'); // Default value for a payment that succeed.
                $message = null; // You can add a comment directly into the order so the merchant will see it in the BO.
                $module_name = $this->module->displayName;
                $currency_id = (int) Context::getContext()->currency->id;

                if ($secure_key != $customer->secure_key) {
                    die(Tools::jsonEncode(['result'=> false, 'stop' => true, 'error' => 'Secure error']));
                }

                $this->module->validateOrder($cart_id, $payment_status, $cart->getOrderTotal(), $module_name, $message, array(), $currency_id, false, $secure_key);

                /**
                 * The order has been placed so we redirect the customer on the confirmation page.
                 */
                $module_id = $this->module->id;
                $order_id = Order::getOrderByCartId((int) $cart->id);
                if (!$order_id) {
                    die(Tools::jsonEncode(['result'=> false, 'stop' => true, 'error' => 'Order creating error']));
                }
                $url = Tools::getHttpHost(true).__PS_BASE_URI__.'/index.php?controller=order-confirmation&id_cart='.$cart_id.'&id_module='.$module_id.'&id_order='.$order_id.'&key='.$secure_key;
                die(Tools::jsonEncode(['result' => true, 'url' => $url, 'status' => $status]));
            }
            die(Tools::jsonEncode(['result' => false, 'status' => $status]));

        } catch (Exception $e) {
            die(Tools::jsonEncode(['result' => false, 'stop' => true, 'error' => $e->getMessage()]));
        }
    }


}
