{*
* 2007-2015 PrestaShop
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<form action="{$action}" id="mercury-payment-form">
    <p>
        <label><b>{l s='Please, choose currency option:'}</b></label>
        <span class="float-xs-left">
            <input type="radio" class="mercury-radio" name="currency_option" id="bitcoin" value="BTC">
        </span>

        <label for="bitcoin">
            <img id="bitcoin_logo">
            <span>Bitcoin</span>
        </label>
        <span class="float-xs-left">
            <input type="radio" class="mercury-radio" name="currency_option" id="etherium" value="ETH">
        </span>
        <label for="etherium">
            <img id="etherium_logo">
            <span>Etherium</span>
        </label>

        <span class="float-xs-left">
            <input type="radio" class="mercury-radio" name="currency_option" id="dash" value="DASH">
        </span>
        <input type="hidden" name="url" value="{$url}">
        <input type="hidden" name="status_url" value="{$status_url}">
        <label for="dash">
            <img id="dash_logo">
            <span>Dash</span>
        </label>
    </p>
</form>
<div class="loader-modal"></div>
<div class="loader-qr">
    <div class="mercury-qr text-center">
        <div class="close-modal">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABmJLR0QA/wD/AP+gvaeTAAADpUlEQVRYhe2Wz29UVRTHP+e+96Y/tKxME2mboLLApE6Zqf0HJA6ZmdqBlF03hl2JSiICK03dCdGoiaJx4UpXQEvB6cQm+A9gW601uoHGUMCwwybj9P24x0UHLe+9dmh1Z7+rl3vOvd/Pfe/cdw/s6v8u2U5yfajQ50ZORVXKAvuAvmbotsKyCNUw1OnOxdrKfwpQHxjucSV6B5HjgNsi3YpwOVROdyzM/PavARq58ohR/QqhC/CBKUWuaBQttGFXANYwveKYvEAFOApkUFat0bH2+dq1HQP4ufLroB8BRoSLYeic7Vi8trwl8FD5WSfU8wqjgEX0ZGa+9sm2ARq58ohBpwBFOJ2Zn/lwK+O4glzxlCLnALGiRzZ7E6kA69/c/oLQhfDmds1jEO8Df3gmOiBz396L55i0ia6x7yJ0iXBxp+YA3kLtA4FJYE8QmYm0nARAfajQB7wK+GHonI3H/VxxXLOF7vi4Zgvdfq44Hh+PjJ4BfESO1weGe1oCuJFTARxgKl5w/sHyCZALgete3wih2UJ34LrXQS6s5/yj9rnaTRGmAdcVrbQEUJUSgMJ0PObZ4BLCEkr/Q4i/zZV+hCXPBpfi86zVaQA1WmoJIPAcgFrmErHF2fteGB7aCPGIeRgeksXZ+4lNWf0eQHR97S0BQPYCtIX1u8lYE0L1JeAnlH6UfuBXz+HlNHOAtqhxpzm7tzWAqqYt0lI22vyn9mRH00dtawDhHsCa27k3bS3NFroDke+AFxCWEJaAA4E6s2mnA2DtT326+Zh4q8kihJsAYhhMNY9983hNpEGIkRcBVNbX3hJAhCpA82J5RIHxjsULLlGYxjuWMDFSARDVasIvPlDPFntdR5YBa115vv1G9dbGuJ8rjntRdDlecJotdAeOM5pZqH22cbyRP7zfqPMzYEJr9nX++M2duGdCfr78hZ8raZArJc70dhUcLE76uZL6udLnafHUuyBQZwJlVWE0yBVP7dy8/JaKHAUeeC6PdxcAPLFw9a41OgZYRc7tBGLdXN8DrFEZkxszv6flbd2Q5IuvofIxYAQmI6Nn2udqiUreqEb+8H7HmvPNnVuENzLzM59ult+yJQvzpWGrfA3sAXwRpq3lCo4zn2msrgD47V29ROGgEXNE0QrgAQ+Mypj7QzVR+dsCANDBV54KbPQ2cILWTWmI6peeYyfSGpAdATxUfWC4xzXRiGKGBX0GpNmW620VbolqNbTO1cc6arvaVVN/AZbxndquXoRBAAAAAElFTkSuQmCC"/>
        </div>
        <h3><b>Amount to be Paid</b></h3>
        <h2 id="crypto_amount"></h2>
        <br>
        <b>Amount to be Paid <span class="current_currency"></span>: </b> <span id="amount"></span><span class="current_currency"></span>
        <br>
        <b>Exchange rate: </b><i id="exchange_rate"></i> <i class="current_currency"></i>
        <br><br>
        <b>Total:</b> <i id="total_amount"></i> <i class="crypto_type"></i>
        <br>
        <div id="qr_code" class="qr-code"></div>
        <div id="crypto_address" style="display: none;"></div>
        <br>
        <button class="btn btn-success" id="show_wallet" data-type="address">Show Wallet Address</button>
        <br><br>
        <b>Suggested network coast:</b> <i id="network_coast"></i><i class="crypto_type"></i>
        <div><h3 class="mercury-timer"></h3></div>
    </div>
</div>
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" style="margin: 0 auto;" id="exampleModalLabel">Order was successfully paid</h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" style="margin: 0 auto;" id="errorModalLabel"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>