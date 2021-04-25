$(document).ready(function () {

    $('#payment-confirmation button').on('click', function(e) {
        if ($('input[name="payment-option"]:checked').attr('data-module-name') === 'mercury_cash') {
            e.preventDefault();
            $('body').prepend('<div id="mercury-cash"></div>');

            var url              = $('input[name="url"]').val();
            var status_url       = $('input[name="status_url"]').val();
            var get_settings_url = $('input[name="get_settings_url"]').val();
            var success_url      = $('input[name="success_url"]').val();
            var refresh_period   = $('input[name="refresh_period"]').val() * 1000;

            $.ajax({
                ajax: 1,
                url: get_settings_url,
                type: 'post',
                dataType: 'json',
                success: function(data) {
                    var price        = data.price;
                    var currency     = data.currency;
                    var minimum_btc  = data.minimum_btc;
                    var minimum_eth  = data.minimum_eth;
                    var minimum_dash = data.minimum_dash;
                    var email        = data.email;

                    var sdk = new MercurySDK({
                        checkoutUrl: url,
                        statusUrl: status_url,
                        checkStatusInterval: refresh_period,
                        mount: '#mercury-cash',
                        lang: 'en',
                        limits: {
                            BTC:  minimum_btc,
                            ETH:  minimum_eth,
                            DASH: minimum_dash
                        }
                    });
                    sdk.checkout(price, currency, email);
                    sdk.on('close', (obj) => {
                        if (obj.status === 'TRANSACTION_APROVED') {
                            $('body').addClass('loading');
                            $.ajax({
                                ajax: 1,
                                url: success_url,
                                type: 'post',
                                dataType: 'json',
                                success: function(data) {
                                    $('body').removeClass('loading');
                                    if (data.result == true) {
                                        var url = data.url;
                                        window.location.href = url;
                                    } else {
                                        $('#mercury-cash').remove();
                                        $('#errorModalLabel').html(data.error);
                                        $('#errorModal').modal('toggle');
                                    }
                                },
                                error: function (jqXHR, exception) {
                                    $('body').removeClass('loading');
                                    var msg = '';
                                    if (jqXHR.status === 0) {
                                        msg = 'Not connect.\n Verify Network.';
                                    } else if (jqXHR.status == 404) {
                                        msg = 'Requested page not found. [404]';
                                    } else if (jqXHR.status == 500) {
                                        msg = 'Internal Server Error [500].';
                                    } else if (exception === 'parsererror') {
                                        msg = 'Requested JSON parse failed.';
                                    } else if (exception === 'timeout') {
                                        msg = 'Time out error.';
                                    } else if (exception === 'abort') {
                                        msg = 'Ajax request aborted.';
                                    } else {
                                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                                    }
                                    $('#errorModalLabel').html(msg);
                                    $('#errorModal').modal('toggle');
                                }
                            });
                        }
                    });
                },
                error: function (jqXHR, exception) {
                    var msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status == 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status == 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    $('#errorModalLabel').html(msg);
                    $('#errorModal').modal('toggle');
                }
            });
            return false;
        }
    });

});