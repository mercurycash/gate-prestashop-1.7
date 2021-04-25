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

            if (mb_strtoupper($status) === 'TRANSACTION_PENDING') {
                $confirmations = 0;
                if ($received_confirmations = $status_object->getConfirmations()) {
                    $confirmations = $received_confirmations;
                }
                die(Tools::jsonEncode([
                    'data' => [
                        'status' => $status,
                        'confirmations' => $confirmations
                    ]
                ]));
            }

            if (mb_strtoupper($status) === 'TRANSACTION_RECEIVED') {
                $confirmations = 0;
                if ($received_confirmations = $status_object->getConfirmations()) {
                    $confirmations = $received_confirmations;
                }
                die(Tools::jsonEncode([
                    'data' => [
                        'status' => $status,
                        'confirmations' => $confirmations
                    ]
                ]));
            }

            if (mb_strtoupper($status) === 'TRANSACTION_APROVED') {
                $confirmations = 0;
                if ($received_confirmations = $status_object->getConfirmations()) {
                    $confirmations = $received_confirmations;
                }
                die(Tools::jsonEncode([
                    'data' => [
                        'status' => $status,
                        'confirmations' => $confirmations
                    ]
                ]));
            }

            die(Tools::jsonEncode([
                'data' => [
                    'error' => 'Unknown Status',
                    'status' => $status
                ]
            ]));
        } catch (Exception $e) {
            die(Tools::jsonEncode([
                'data' => [
                    'error' => $e->getMessage()
                ]
            ]));
        }
    }

}