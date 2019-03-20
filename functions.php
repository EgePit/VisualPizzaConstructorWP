<?php
function vconst_get_currency() {
    return get_woocommerce_currency_symbol();
}

function get_available_gateways() {
    $available_gateways = WC()->payment_gateways->get_available_payment_gateways();
    $payments = array();
    foreach($available_gateways as $gateway_name=> $gateway) {
        $payments[$gateway_name] = $gateway->title;
    }

    return $payments;
}