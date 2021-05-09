<?php

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