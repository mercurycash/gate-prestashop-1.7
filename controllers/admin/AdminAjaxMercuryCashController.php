<?php


class AdminAjaxMercuryCashController extends ModuleAdminController
{
    public function ajaxProcessTransactionCreate()
    {
        header('Content-Type: application/json');
        die(Tools::jsonEncode(['result'=> true, 'id' => 'test', 'otp_code' => 'test']));
    }
}