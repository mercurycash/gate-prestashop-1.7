<?php

class MercuryCashRedirectModuleFrontController extends ModuleFrontController
{

    /**
     * Do whatever you have to before redirecting the customer on the website of your payment processor.
     */
    public function postProcess()
    {
        if (Tools::getValue('action') == 'error') {
            return $this->displayError('An error occurred while trying to redirect the customer');
        }

        $this->context->smarty->assign(array(
            'cart_id' => Context::getContext()->cart->id,
            'secure_key' => Context::getContext()->customer->secure_key,
        ));

        return $this->setTemplate('redirect.tpl');
    }

    /**
     * @param      $message
     * @param bool $description
     *
     * @throws \PrestaShopException
     */
    protected function displayError($message, $description = false)
    {
        /*
         * Create the breadcrumb for your ModuleFrontController.
         */
        $this->context->smarty->assign('path', '
			<a href="' . $this->context->link->getPageLink('order', null, null, 'step=3') . '">' . $this->module->l('Payment') . '</a>
			<span class="navigation-pipe">&gt;</span>' . $this->module->l('Error'));

        /*
         * Set error message and description for the template.
         */
        array_push($this->errors, $this->module->l($message), $description);

        return $this->setTemplate('error.tpl');
    }

}
