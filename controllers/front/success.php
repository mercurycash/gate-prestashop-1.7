<?php

class MercuryCashSuccessModuleFrontController extends ModuleFrontController
{

    /**
     *
     */
    public function postProcess()
    {
        try {
            /*
             * If the module is not active anymore, no need to process anything.
             */
            if ($this->module->active == false) {
                die(Tools::jsonEncode(['result' => false, 'error' => 'Module not active']));
            }
            $context = Context::getContext();

            $id_cart = $this->context->cookie->__get('id_cart');
            $secure_key = $context->customer->secure_key;
            $cart     = new Cart((int) $id_cart);
            $customer = new Customer((int) $cart->id_customer);
            $payment_status = Configuration::get('PS_OS_PAYMENT'); // Default value for a payment that succeed.
            $message = null; // You can add a comment directly into the order so the merchant will see it in the BO.
            $module_name = $this->module->displayName;
            $currency_id = (int) $context->currency->id;
            if ($secure_key != $customer->secure_key) {
                die(Tools::jsonEncode(['result' => false, 'error' => 'Secure error']));
            }
            $this->module->validateOrder($id_cart, $payment_status, $cart->getOrderTotal(), $module_name, $message, array(), $currency_id, false, $secure_key);
            /**
             * The order has been placed so we redirect the customer on the confirmation page.
             */
            $module_id = $this->module->id;
            $order_id = Order::getOrderByCartId((int) $cart->id);
            if (!$order_id) {
                die(Tools::jsonEncode(['result' => false, 'error' => 'Order creating error']));
            }
            $url = Tools::getHttpHost(true).__PS_BASE_URI__.'/index.php?controller=order-confirmation&id_cart='.$id_cart.'&id_module='.$module_id.'&id_order='.$order_id.'&key='.$secure_key;
            die(Tools::jsonEncode(['result' => true, 'url' => $url]));
        } catch (Exception $e) {
            die(Tools::jsonEncode(['result' => false, 'error' => $e->getMessage()]));
        }
    }

}