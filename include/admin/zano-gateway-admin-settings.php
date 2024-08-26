<?php

defined( 'ABSPATH' ) || exit;

return array(
    'enabled' => array(
        'title' => __('Enable / Disable', 'zano_gateway'),
        'label' => __('Enable this payment gateway', 'zano_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
    ),
    'title' => array(
        'title' => __('Title', 'zano_gateway'),
        'type' => 'text',
        'desc_tip' => __('Payment title the customer will see during the checkout process.', 'zano_gateway'),
        'default' => __('Zano Gateway', 'zano_gateway')
    ),
    'description' => array(
        'title' => __('Description', 'zano_gateway'),
        'type' => 'textarea',
        'desc_tip' => __('Payment description the customer will see during the checkout process.', 'zano_gateway'),
        'default' => __('Pay securely using Pay securely using <a href="https://zano.org/" target="self">Zano</a>. You will be provided payment details after checkout.', 'zano_gateway')
    ),
    'discount' => array(
        'title' => __('Discount for using Zano', 'zano_gateway'),
        'desc_tip' => __('Provide a discount to your customers for making a private payment with Zano', 'zano_gateway'),
        'description' => __('Enter a percentage discount (i.e. 5 for 5%) or leave this empty if you do not wish to provide a discount', 'zano_gateway'),
        'type' => __('number'),
        'default' => '0'
    ),
    'valid_time' => array(
        'title' => __('Order valid time', 'zano_gateway'),
        'desc_tip' => __('Amount of time order is valid before expiring', 'zano_gateway'),
        'description' => __('Enter the number of seconds that the funds must be received in after order is placed. 3600 seconds = 1 hour', 'zano_gateway'),
        'type' => __('number'),
        'default' => '3600'
    ),
    'confirms' => array(
        'title' => __('Number of confirmations', 'zano_gateway'),
        'desc_tip' => __('Number of confirms a transaction must have to be valid', 'zano_gateway'),
        'description' => __('Enter the number of confirms that transactions must have. Enter 0 to zero-confim. Each confirm will take approximately four minutes', 'zano_gateway'),
        'type' => __('number'),
        'default' => '5'
    ),
    'confirm_type' => array(
        'title' => __('Confirmation Type', 'zano_gateway'),
        'desc_tip' => __('Select the method for confirming transactions', 'zano_gateway'),
        'description' => __('Select the method for confirming transactions', 'zano_gateway'),
        'type' => 'select',
        'options' => array(
            'viewkey'        => __('viewkey', 'zano_gateway'),
            'zano-wallet-rpc' => __('zano-wallet-rpc', 'zano_gateway')
        ),
        'default' => 'viewkey'
    ),
    'zano_address' => array(
        'title' => __('Zano Address', 'zano_gateway'),
        'label' => __('Useful for people that have not a daemon online'),
        'type' => 'text',
        'desc_tip' => __('Zano Wallet Address (ZanoL)', 'zano_gateway')
    ),
    'viewkey' => array(
        'title' => __('Secret Viewkey', 'zano_gateway'),
        'label' => __('Secret Viewkey'),
        'type' => 'text',
        'desc_tip' => __('Your secret Viewkey', 'zano_gateway')
    ),
    'daemon_host' => array(
        'title' => __('Zano wallet RPC Host/IP', 'zano_gateway'),
        'type' => 'text',
        'desc_tip' => __('This is the Daemon Host/IP to authorize the payment with', 'zano_gateway'),
        'default' => '127.0.0.1',
    ),
    'daemon_port' => array(
        'title' => __('Zano Wallet RPC port', 'zano_gateway'),
        'type' => __('number'),
        'desc_tip' => __('This is the Wallet RPC port to authorize the payment with', 'zano_gateway'),
        'default' => '11212',
    ),
    'daemon_host_port' => array(
        'title' => __('Zano Daemon RPC port', 'zano_gateway'),
        'type' => __('number'),
        'desc_tip' => __('This is the Daemon RPC port to connect the deamon', 'zano_gateway'),
        'default' => '11211',
    ),
    'testnet' => array(
        'title' => __(' Testnet', 'zano_gateway'),
        'label' => __(' Check this if you are using testnet ', 'zano_gateway'),
        'type' => 'checkbox',
        'description' => __('Advanced usage only', 'zano_gateway'),
        'default' => 'no'
    ),
    'javascript' => array(
        'title' => __(' Javascript', 'zano_gateway'),
        'label' => __(' Check this to ENABLE Javascript in Checkout page ', 'zano_gateway'),
        'type' => 'checkbox',
        'default' => 'no'
     ),
    'onion_service' => array(
        'title' => __(' SSL warnings ', 'zano_gateway'),
        'label' => __(' Check to Silence SSL warnings', 'zano_gateway'),
        'type' => 'checkbox',
        'description' => __('Check this box if you are running on an Onion Service (Suppress SSL errors)', 'zano_gateway'),
        'default' => 'no'
    ),
    'show_qr' => array(
        'title' => __('Show QR Code', 'zano_gateway'),
        'label' => __('Show QR Code', 'zano_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to show a QR code after checkout with payment details.'),
        'default' => 'no'
    ),
    'use_zano_price' => array(
        'title' => __('Show Prices in Zano', 'zano_gateway'),
        'label' => __('Show Prices in Zano', 'zano_gateway'),
        'type' => 'checkbox',
        'description' => __('Enable this to convert ALL prices on the frontend to Zano (experimental)'),
        'default' => 'no'
    ),
    'use_zano_price_decimals' => array(
        'title' => __('Display Decimals', 'zano_gateway'),
        'type' => __('number'),
        'description' => __('Number of decimal places to display on frontend. Upon checkout exact price will be displayed.'),
        'default' => 12,
    ),
);
