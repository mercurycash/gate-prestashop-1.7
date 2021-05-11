<?php

require_once('vendor/autoload.php');
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;


if (!defined('_PS_VERSION_')) {
    exit;
}

class MercuryCash extends PaymentModule
{

    protected $config_form = false;

    /**
     * MercuryCash constructor.
     */
    public function __construct()
    {
        $this->name          = 'mercurycash';
        $this->tab           = 'payments_gateways';
        $this->version       = '1.0.0';
        $this->author        = '2V Modules';
        $this->need_instance = 0;
        $this->active        = true;
        $this->currencies    = true;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Mercury Cash');
        $this->description = $this->l('Module For Mercury Cash payments');

        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {

        include(dirname(__FILE__).'/sql/install.php');

        Configuration::updateValue('MERCURYCASH_STATUS_PERIOD', 5);
        Configuration::updateValue('MERCURYCASH_BITCOIN_MIN', 40);
        Configuration::updateValue('MERCURYCASH_ETHEREUM_MIN', 5);
        Configuration::updateValue('MERCURYCASH_DASH_MIN', 2);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('payment') &&
            $this->registerHook('paymentReturn') &&
            $this->registerHook('paymentOptions');
    }


    /**
     * @return bool
     */
    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }


    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitMercuryCashModule')) == true) {
            $this->postProcess();
        }

        return $this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar             = false;
        $helper->table                    = $this->table;
        $helper->module                   = $this;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitMercuryCashModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Status refresh period (seconds)'),
                        'name' => 'MERCURYCASH_STATUS_PERIOD',
                        'label' => $this->l('Status refresh period (seconds)'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter an API public key'),
                        'name' => 'MERCURYCASH_PUBLIC_KEY',
                        'label' => $this->l('Public key'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter an API private key'),
                        'name' => 'MERCURYCASH_PRIVATE_KEY',
                        'label' => $this->l('Private key'),
                    ),
                    array(
                        'type' => 'radio',
                        'label' => $this->l('Enable Sandbox'),
                        'name' => 'MERCURYCASH_SANDBOX',
                        'values' => array(
                            array(
                                'id' => 'enable_sandbox',
                                'value' => 1,
                                'label' => $this->l('Enable')
                            ),
                            array(
                                'id' => 'disable_sandbox',
                                'value' => 0,
                                'label' => $this->l('Disable')
                            )
                        ),
                        'is_bool' => true,
                        'required' => true
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter an API public key for Sandbox'),
                        'name' => 'MERCURYCASH_PUBLIC_KEY_SANDBOX',
                        'label' => $this->l('Public key for Sandbox'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Enter an API private key for Sandbox'),
                        'name' => 'MERCURYCASH_PRIVATE_KEY_SANDBOX',
                        'label' => $this->l('Private key for Sandbox'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Bitcoin minimum'),
                        'name' => 'MERCURYCASH_BITCOIN_MIN',
                        'label' => $this->l('Bitcoin minimum'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Ethereum minimum'),
                        'name' => 'MERCURYCASH_ETHEREUM_MIN',
                        'label' => $this->l('Ethereum minimum'),
                    ),
                    array(
                        'col' => 3,
                        'type' => 'text',
                        'desc' => $this->l('Dash minimum'),
                        'name' => 'MERCURYCASH_DASH_MIN',
                        'label' => $this->l('Dash minimum'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }


    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'MERCURYCASH_STATUS_PERIOD'       => Configuration::get('MERCURYCASH_STATUS_PERIOD', null, null, null, 5),
            'MERCURYCASH_PUBLIC_KEY'          => Configuration::get('MERCURYCASH_PUBLIC_KEY'),
            'MERCURYCASH_PRIVATE_KEY'         => Configuration::get('MERCURYCASH_PRIVATE_KEY'),
            'MERCURYCASH_PUBLIC_KEY_SANDBOX'  => Configuration::get('MERCURYCASH_PUBLIC_KEY_SANDBOX'),
            'MERCURYCASH_PRIVATE_KEY_SANDBOX' => Configuration::get('MERCURYCASH_PRIVATE_KEY_SANDBOX'),
            'MERCURYCASH_SANDBOX'             => Configuration::get('MERCURYCASH_SANDBOX'),
            'MERCURYCASH_BITCOIN_MIN'         => Configuration::get('MERCURYCASH_BITCOIN_MIN', null, null, null, 40),
            'MERCURYCASH_ETHEREUM_MIN'        => Configuration::get('MERCURYCASH_ETHEREUM_MIN', null, null, null, 5),
            'MERCURYCASH_DASH_MIN'            => Configuration::get('MERCURYCASH_DASH_MIN', null, null, null, 2),
        );
    }


    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        $public_key  = Tools::getValue('MERCURYCASH_PUBLIC_KEY');
        $private_key = Tools::getValue('MERCURYCASH_PRIVATE_KEY');
        $sandbox     = Tools::getValue('MERCURYCASH_SANDBOX');
        $period      = Tools::getValue('MERCURYCASH_STATUS_PERIOD');

        $this->clearNotifications();
        $period_error = !$this->checkPeriod($period);

        $credentials_error = !$this->checkCredentials($public_key, $private_key);

        if ($sandbox) {
            $public_key = Tools::getValue('MERCURYCASH_PUBLIC_KEY_SANDBOX');
            $private_key = Tools::getValue('MERCURYCASH_PRIVATE_KEY_SANDBOX');
            $sb_credentials_error = !$this->checkSandboxCredentials($public_key, $private_key);
        }

        if ($period_error) {
            $this->context->controller->errors[] = 'Wrong Period value';
        }

        $this->updateConfiguration($form_values, $credentials_error, $sb_credentials_error, $period_error);
    }


    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }


    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addJS($this->_path.'/mercury-cash-react/build/static/js/main.3d3f7b08.js');
        $this->context->controller->addCSS($this->_path.'/mercury-cash-react/build/static/css/main.d17b8e8c.css');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }


    /**
     * Return payment options available for PS 1.7+
     *
     * @param array Hook parameters
     *
     * @return array|null
     * @throws \Exception
     */
    public function hookPaymentOptions($params)
    {
        $check_minimum_amount = false;
        $currencies_check = $this->checkCurrencies();

        if (!$this->active || !$currencies_check || !$this->checkCurrency($params['cart'])) {
            return;
        }

        $minimum_amount_array = $this->getMinimumAmountArray();
        $amount = $this->context->cart->getOrderTotal();

        foreach ($minimum_amount_array as $minimum_amount) {
            if ($amount >= $minimum_amount) {
                $check_minimum_amount = true;
                break;
            }
        }

        if (!$check_minimum_amount) {
            return;
        }
        return [
            $this->getEmbeddedPaymentOption(),
        ];
    }

    /**
     * @return \PrestaShop\PrestaShop\Core\Payment\PaymentOption
     */
    public function getEmbeddedPaymentOption()
    {
        $embeddedOption = new PaymentOption();
        $embeddedOption->setCallToActionText($this->l('Mercury cash'))
            ->setModuleName('mercury_cash')
            ->setForm($this->generateForm());
        return $embeddedOption;
    }

    /**
     * @return mixed
     */
    protected function generateForm()
    {
        $process_url  = $this->context->link->getModuleLink($this->name, 'validation', array('ajax' => true));
        $status_url   = $this->context->link->getModuleLink($this->name, 'check',      array('ajax' => true));
        $settings_url = $this->context->link->getModuleLink($this->name, 'settings',   array('ajax' => true));
        $success_url  = $this->context->link->getModuleLink($this->name, 'success',    array('ajax' => true));

        $refresh_period = Configuration::get('MERCURYCASH_STATUS_PERIOD', null, null, null, 5);

        $this->context->smarty->assign([
            'get_settings_url' => $settings_url,
            'success_url' => $success_url,
            'action' => $this->context->link->getModuleLink($this->name, 'validation', array(), true),
            'url' => $process_url,
            'status_url' => $status_url,
            'refresh_period' => $refresh_period
        ]);

        return $this->context->smarty->fetch('module:mercurycash/views/templates/front/payment_form.tpl');
    }


    /**
     * @param $cart
     *
     * @return bool
     */
    public function checkCurrency($cart)
    {
        $currency_order = new Currency($cart->id_currency);
        $currencies_module = $this->getCurrency($cart->id_currency);
        if (is_array($currencies_module)) {
            foreach ($currencies_module as $currency_module) {
                if ($currency_order->id == $currency_module['id_currency']) {
                    return true;
                }
            }
        }
        return false;
    }


    /**
     * @param $type
     *
     * @return int|string
     */
    public function getMinimumAmount(string $type)
    {
        switch ($type) {
            case 'BTC' :
                return Configuration::get('MERCURYCASH_BITCOIN_MIN', null, null, null, 40);
            case 'ETC' :
                return Configuration::get('MERCURYCASH_ETHEREUM_MIN', null, null, null, 5);
            case 'DASH' :
                return Configuration::get('MERCURYCASH_DASH_MIN', null, null, null, 2);
            default:
                return 0;
        }
    }


    /**
     * @return array
     */
    public function getMinimumAmountArray()
    {
        return [
            $this->getMinimumAmount('BTC'),
            $this->getMinimumAmount('ETC'),
            $this->getMinimumAmount('DASH'),
        ];
    }


    /**
     * @return string
     */
    private function getPublicKey()
    {
        return Configuration::get('MERCURYCASH_SANDBOX', null) ?
            Configuration::get('MERCURYCASH_PUBLIC_KEY_SANDBOX', '') : Configuration::get('MERCURYCASH_PUBLIC_KEY', '');
    }


    /**
     * @return string
     */
    private function getPrivateKey()
    {
        return Configuration::get('MERCURYCASH_SANDBOX', null) ?
            Configuration::get('MERCURYCASH_PRIVATE_KEY_SANDBOX', '') : Configuration::get('MERCURYCASH_PRIVATE_KEY', '');
    }


    /**
     * @return string
     */
    public function isSandbox()
    {
        return Configuration::get('MERCURYCASH_SANDBOX', false);
    }


    /**
     * @param null $public_key_
     * @param null $private_key_
     *
     * @return \MercuryCash\SDK\Auth\APIKey|null
     */
    public function getApiKey($public_key_ = null, $private_key_ = null)
    {
        $public_key  = $public_key_  ? $public_key_  : $this->getPublicKey();
        $private_key = $private_key_ ? $private_key_ : $this->getPrivateKey();

        return $public_key && $private_key ?
            new \MercuryCash\SDK\Auth\APIKey($public_key, $private_key) : null;
    }


    /**
     * @return array
     */
    public function getAvailableCurrencies()
    {
        try {
            $api_key = $this->getApiKey();
            $adapter_for_currencies = new \MercuryCash\SDK\Adapter($api_key, 'https://api.mercury.cash');
            $endpoint = new \MercuryCash\SDK\Endpoints\Transaction($adapter_for_currencies);

            $currencies = $endpoint->currenciesData();
            $available_currencies = [];
            if ($currencies && is_array($currencies) && isset($currencies['data'])) {
                foreach ($currencies['data'] as $key => $value) {
                    if (is_array($value) && isset($value['exchange'])) {
                        $available_currencies[$key] = $value;
                    }
                }
            }
            return $available_currencies;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * @param $form_values
     * @param $credentials_error
     * @param $sb_credentials_error
     * @param $period_error
     */
    private function updateConfiguration($form_values, $credentials_error, $sb_credentials_error, $period_error)
    {
        foreach (array_keys($form_values) as $key) {
            $value = Tools::getValue($key);
            if ($this->ifCredentialsError($credentials_error, $key) || $this->ifSandboxCredentialsError($sb_credentials_error, $key)) {
                $value = null;
            }
            if ($this->ifPeriodError($period_error, $key)) {
                $value = 5;
            }
            Configuration::updateValue($key, $value);
        }
    }

    /**
     * @param $credentials_error
     * @param $key
     *
     * @return bool
     */
    private function ifCredentialsError($credentials_error, $key)
    {
        return $credentials_error && ($key === 'MERCURYCASH_PUBLIC_KEY' || $key === 'MERCURYCASH_PRIVATE_KEY');
    }

    /**
     * @param $credentials_error
     * @param $key
     *
     * @return bool
     */
    private function ifSandboxCredentialsError($credentials_error, $key)
    {
        return $credentials_error && ($key === 'MERCURYCASH_PUBLIC_KEY_SANDBOX' || $key === 'MERCURYCASH_PRIVATE_KEY_SANDBOX');
    }

    /**
     * @param $period_error
     * @param $key
     *
     * @return bool
     */
    private function ifPeriodError($period_error, $key)
    {
        return $period_error && $key === 'MERCURYCASH_STATUS_PERIOD';
    }

    /**
     * @param $period
     *
     * @return bool
     */
    private function checkPeriod($period)
    {
        return is_numeric($period) && $period >= 1 && $period <= 15;
    }

    /**
     *
     */
    private function clearNotifications()
    {
        $this->context->controller->errors = [];
        $this->context->controller->confirmations = [];
    }

    /**
     * @param $public_key
     * @param $private_key
     *
     * @return bool
     */
    private function checkCredentials($public_key, $private_key)
    {
        try {
            if (!$public_key || !$private_key) {
                throw new Exception('wrong credentials');
            }
            $api_key  = $this->getApiKey($public_key, $private_key);
            $adapter  = new \MercuryCash\SDK\Adapter($api_key);
            $endpoint = new \MercuryCash\SDK\Endpoints\Transaction($adapter);
            $endpoint->status('test');
            $this->context->controller->confirmations[] = 'Mercury credentials were verified';
        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            if (strpos($exception->getMessage(), '[status code] 424') !== false) {
                $this->context->controller->errors[] = 'Wrong Mercury credentials';
                return false;
            }
            $this->context->controller->confirmations[] = 'Mercury credentials were verified';
        } catch (\GuzzleHttp\Exception\ServerException $exception) {
            if ($this->checkIfNotException($exception)) {
                return false;
            }
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            if ($this->checkIfNotException($exception)) {
                return false;
            }
        } catch (Exception $exception) {
            $this->context->controller->errors[] = 'Wrong Mercury credentials';
            return false;
        }
        return true;
    }

    /**
     * @param $public_key
     * @param $private_key
     *
     * @return bool
     */
    private function checkSandboxCredentials($public_key, $private_key)
    {
        try {
            if (!$public_key || !$private_key) {
                throw new Exception('wrong credentials');
            }
            $api_key = $this->getApiKey($public_key, $private_key);
            $adapter = $this->isSandbox() ?
                new \MercuryCash\SDK\Adapter($api_key, 'https://api-way.mercurydev.tk') :
                new \MercuryCash\SDK\Adapter($api_key);
            $endpoint = new \MercuryCash\SDK\Endpoints\Transaction($adapter);
            $endpoint->status('');
            $this->context->controller->errors = [];
        } catch (\GuzzleHttp\Exception\BadResponseException $exception) {
            if (strpos($exception->getMessage(), '[status code] 424') !== false) {
                $this->context->controller->errors[] = 'Wrong Mercury credentials for Sandbox';
                return false;
            }
            $this->context->controller->confirmations[] = 'Mercury credentials for Sandbox were verified';
        } catch (\GuzzleHttp\Exception\ServerException $exception) {
            if ($this->checkIfNotException($exception, true)) {
                return false;
            }
        } catch (\GuzzleHttp\Exception\ClientException $exception) {
            if ($this->checkIfNotException($exception, true)) {
                return false;
            }
        } catch (Exception $exception) {
            $this->context->controller->errors[] = 'Wrong Mercury credentials for Sandbox';
            return false;
        }
        return true;
    }

    /**
     * @param      $exception
     *
     * @param bool $is_sandbox
     *
     * @return bool
     */
    private function checkIfNotException($exception, $is_sandbox = false)
    {
        $response = $exception->getResponse();
        if ($response->getStatusCode() == 500) {
            $this->context->controller->errors[] = $is_sandbox ?
                'Wrong Mercury credentials for Sandbox' : 'Wrong Mercury credentials';
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    private function checkCurrencies()
    {
        $module_currencies = $this->getAvailableCurrencies();

        if (!$module_currencies) {
            return false;
        }
        $module_currencies_array = array_keys($module_currencies);
        $currencies = CurrencyCore::getCurrencies();

        if ($currencies && is_array($currencies)) {
            foreach ($currencies as $currency) {
                if (in_array($currency['iso_code'], $module_currencies_array)) {
                    return true;
                }
            }
        }
        return false;
    }

}
