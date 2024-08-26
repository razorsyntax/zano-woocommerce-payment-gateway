<?php
/*
Forked from Monero WooCommerce Gateway by mosu-forge and SerHack

Plugin Name: Zano WooCommerce Gateway
Plugin URI: [insert github link]
Description: Extends WooCommerce by adding a Zano Gateway
Version: 0.0.1
Tested up to: 0.0.1
Author: razorsyntax
Author URI: 
*/
// This code is for everyone. Use at your own risk.

defined( 'ABSPATH' ) || exit;

// Constants, you can edit these if you fork this repo
define('ZANO_GATEWAY_MAINNET_EXPLORER_URL', 'https://explorer.zano.org/');
define('ZANO_GATEWAY_TESTNET_EXPLORER_URL', 'https://testnet-explorer.zano.org/');
define('ZANO_GATEWAY_ADDRESS_PREFIX', 0x12);
define('ZANO_GATEWAY_ADDRESS_PREFIX_INTEGRATED', 0x13);
define('ZANO_GATEWAY_ATOMIC_UNITS', 12);
define('ZANO_GATEWAY_ATOMIC_UNIT_THRESHOLD', 10); // Amount under in atomic units payment is valid
define('ZANO_GATEWAY_DIFFICULTY_TARGET', 120);

// Do not edit these constants
define('ZANO_GATEWAY_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ZANO_GATEWAY_PLUGIN_URL', plugin_dir_url(__FILE__));
define('ZANO_GATEWAY_ATOMIC_UNITS_POW', pow(10, ZANO_GATEWAY_ATOMIC_UNITS));
define('ZANO_GATEWAY_ATOMIC_UNITS_SPRINTF', '%.'.ZANO_GATEWAY_ATOMIC_UNITS.'f');

// Include our Gateway Class and register Payment Gateway with WooCommerce
add_action('plugins_loaded', 'zano_init', 1);
function zano_init() {

    // If the class doesn't exist (== WooCommerce isn't installed), return NULL
    if (!class_exists('WC_Payment_Gateway')) return;

    // If we made it this far, then include our Gateway Class
    require_once('include/class-zano-gateway.php'); 

    // Create a new instance of the gateway so we have static variables set up
    new Zano_Gateway($add_action=false);

    // Include our Admin interface class
    require_once('include/admin/class-zano-admin-interface.php');

    add_filter('woocommerce_payment_gateways', 'zano_gateway');
    function zano_gateway($methods) {
        $methods[] = 'Zano_Gateway';
        return $methods;
    }

    add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'zano_payment');
    function zano_payment($links) {
        $plugin_links = array(
            '<a href="'.admin_url('admin.php?page=zano_gateway_settings').'">'.__('Settings', 'zano_gateway').'</a>'
        );
        return array_merge($plugin_links, $links);
    }

    add_filter('cron_schedules', 'zano_cron_add_one_minute');
    function zano_cron_add_one_minute($schedules) {
        $schedules['one_minute'] = array(
            'interval' => 60,
            'display' => __('Once every minute', 'zano_gateway')
        );
        return $schedules;
    }

    add_action('wp', 'zano_activate_cron');
    function zano_activate_cron() {
        if(!wp_next_scheduled('zano_update_event')) {
            wp_schedule_event(time(), 'one_minute', 'zano_update_event');
        }
    }

    add_action('zano_update_event', 'zano_update_event');
    function zano_update_event() {
        Zano_Gateway::do_update_event();
    }

    add_action('woocommerce_thankyou_'.Zano_Gateway::get_id(), 'zano_order_confirm_page');
    add_action('woocommerce_order_details_after_order_table', 'zano_order_page');
    add_action('woocommerce_email_after_order_table', 'zano_order_email');

    function zano_order_confirm_page($order_id) {
        Zano_Gateway::customer_order_page($order_id);
    }
    function zano_order_page($order) {
        if(!is_wc_endpoint_url('order-received'))
            Zano_Gateway::customer_order_page($order);
    }
    function zano_order_email($order) {
        Zano_Gateway::customer_order_email($order);
    }

    add_action('wc_ajax_zano_gateway_payment_details', 'zano_get_payment_details_ajax');
    function zano_get_payment_details_ajax() {
        Zano_Gateway::get_payment_details_ajax();
    }

    add_filter('woocommerce_currencies', 'zano_add_currency');
    function zano_add_currency($currencies) {
        $currencies['Zano'] = __('Zano', 'zano_gateway');
        return $currencies;
    }

    add_filter('woocommerce_currency_symbol', 'zano_add_currency_symbol', 10, 2);
    function zano_add_currency_symbol($currency_symbol, $currency) {
        switch ($currency) {
        case 'Zano':
            $currency_symbol = 'ZANO';
            break;
        }
        return $currency_symbol;
    }

    if(Zano_Gateway::use_zano_price()) {

        // This filter will replace all prices with amount in Zano (live rates)
        add_filter('wc_price', 'zano_live_price_format', 10, 3);
        function zano_live_price_format($price_html, $price_float, $args) {
            $price_float = wc_format_decimal($price_float);
            if(!isset($args['currency']) || !$args['currency']) {
                global $woocommerce;
                $currency = strtoupper(get_woocommerce_currency());
            } else {
                $currency = strtoupper($args['currency']);
            }
            return Zano_Gateway::convert_wc_price($price_float, $currency);
        }

        // These filters will replace the live rate with the exchange rate locked in for the order
        // We must be careful to hit all the hooks for price displays associated with an order,
        // else the exchange rate can change dynamically (which it should for an order)
        add_filter('woocommerce_order_formatted_line_subtotal', 'zano_order_item_price_format', 10, 3);
        function zano_order_item_price_format($price_html, $item, $order) {
            return Zano_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_formatted_order_total', 'zano_order_total_price_format', 10, 2);
        function zano_order_total_price_format($price_html, $order) {
            return Zano_Gateway::convert_wc_price_order($price_html, $order);
        }

        add_filter('woocommerce_get_order_item_totals', 'zano_order_totals_price_format', 10, 3);
        function zano_order_totals_price_format($total_rows, $order, $tax_display) {
            foreach($total_rows as &$row) {
                $price_html = $row['value'];
                $row['value'] = Zano_Gateway::convert_wc_price_order($price_html, $order);
            }
            return $total_rows;
        }

    }

    add_action('wp_enqueue_scripts', 'zano_enqueue_scripts');
    function zano_enqueue_scripts() {
        if(Zano_Gateway::use_zano_price())
            wp_dequeue_script('wc-cart-fragments');
        if(Zano_Gateway::use_qr_code())
            wp_enqueue_script('zano-qr-code', ZANO_GATEWAY_PLUGIN_URL.'assets/js/qrcode.min.js');

        wp_enqueue_script('zano-clipboard-js', ZANO_GATEWAY_PLUGIN_URL.'assets/js/clipboard.min.js');
        wp_enqueue_script('zano-gateway', ZANO_GATEWAY_PLUGIN_URL.'assets/js/zano-gateway-order-page.js');
        wp_enqueue_style('zano-gateway', ZANO_GATEWAY_PLUGIN_URL.'assets/css/zano-gateway-order-page.css');
    }

    // [zano-price currency="USD"]
    // currency: BTC, GBP, etc
    // if no none, then default store currency
    function zano_price_func( $atts ) {
        global  $woocommerce;
        $a = shortcode_atts( array(
            'currency' => get_woocommerce_currency()
        ), $atts );

        $currency = strtoupper($a['currency']);
        $rate = Zano_Gateway::get_live_rate($currency);
        if($currency == 'BTC')
            $rate_formatted = sprintf('%.8f', $rate / 1e8);
        else
            $rate_formatted = sprintf('%.5f', $rate / 1e8);

        return "<span class=\"zano-price\">1 ZANO = $rate_formatted $currency</span>";
    }
    add_shortcode('zano-price', 'zano_price_func');


    // [zano-accepted-here]
    function zano_accepted_func() {
        return '<img src="'.ZANO_GATEWAY_PLUGIN_URL.'assets/images/zano-accepted-here.png" />';
    }
    add_shortcode('zano-accepted-here', 'zano_accepted_func');

}

register_deactivation_hook(__FILE__, 'zano_deactivate');
function zano_deactivate() {
    $timestamp = wp_next_scheduled('zano_update_event');
    wp_unschedule_event($timestamp, 'zano_update_event');
}

register_activation_hook(__FILE__, 'zano_install');
function zano_install() {
    global $wpdb;
    require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
    $charset_collate = $wpdb->get_charset_collate();

    $table_name = $wpdb->prefix . "zano_gateway_quotes";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               order_id BIGINT(20) UNSIGNED NOT NULL,
               payment_id VARCHAR(95) DEFAULT '' NOT NULL,
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               paid TINYINT NOT NULL DEFAULT 0,
               confirmed TINYINT NOT NULL DEFAULT 0,
               pending TINYINT NOT NULL DEFAULT 1,
               created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (order_id)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "zano_gateway_quotes_txids";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
               payment_id VARCHAR(95) DEFAULT '' NOT NULL,
               txid VARCHAR(64) DEFAULT '' NOT NULL,
               amount BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               height MEDIUMINT UNSIGNED NOT NULL DEFAULT 0,
               PRIMARY KEY (id),
               UNIQUE KEY (payment_id, txid, amount)
               ) $charset_collate;";
        dbDelta($sql);
    }

    $table_name = $wpdb->prefix . "zano_gateway_live_rates";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "CREATE TABLE $table_name (
               currency VARCHAR(6) DEFAULT '' NOT NULL,
               rate BIGINT UNSIGNED DEFAULT 0 NOT NULL,
               updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
               PRIMARY KEY (currency)
               ) $charset_collate;";
        dbDelta($sql);
    }
}
