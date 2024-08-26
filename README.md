# ⚠️ Caution ⚠️

## This is a work in progress and we're looking for contributors.

We're stilling porting from the Monero Gateway for WooCommerce.

# Zano Gateway for WooCommerce
### A fork based of of the Monero Gateway for WooCommerce



## Features

* Payment validation done through either `zano-wallet-rpc` or the [explorer.zano.org/ blockchain explorer](https://explorer.zano.org/).
* Validates payments with `cron`, so does not require users to stay on the order confirmation page for their order to validate.
* Order status updates are done through AJAX instead of Javascript page reloads.
* Customers can pay with multiple transactions and are notified as soon as transactions hit the mempool.
* Configurable block confirmations, from `0` for zero confirm to `60` for high ticket purchases.
* Live price updates every minute; total amount due is locked in after the order is placed for a configurable amount of time (default 60 minutes) so the price does not change after order has been made.
* Hooks into emails, order confirmation page, customer order history page, and admin order details page.
* View all payments received to your wallet with links to the blockchain explorer and associated orders.
* Optionally display all prices on your store in terms of Zano.
* Shortcodes! Display exchange rates in numerous currencies.

## Requirements

* Zano wallet to receive payments - [GUI](https://zano.org/downloads) - [CLI]() - [Paper]()
* [BCMath](http://php.net/manual/en/book.bc.php) - A PHP extension used for arbitrary precision maths

## Installing the plugin

### Automatic Method

In the "Add Plugins" section of the WordPress admin UI, search for "zano" and click the Install Now button next to "Zano WooCommerce Extension" by razorsyntax.  This will enable auto-updates, but only for official releases, so if you need to work from git master or your local fork, please use the manual method below.

### Manual Method

* Download the plugin from the [releases page](https://github.com/.....) or clone with `git clone https://github.com/.....`
* Unzip or place the `zano-woocommerce-gateway` folder in the `wp-content/plugins` directory.
* Activate "Zano Woocommerce Gateway" in your WordPress admin dashboard.
* It is highly recommended that you use native cronjobs instead of WordPress's "Poor Man's Cron" by adding `define('DISABLE_WP_CRON', true);` into your `wp-config.php` file and adding `* * * * * wget -q -O - https://yourstore.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1` to your crontab.

## Option 1: Use your wallet address and viewkey

This is the easiest way to start accepting Zano on your website. You'll need:

* Create an [auditable wallet](https://docs.zano.org/docs/use/auditable-wallets-faq)
* Your audiable Zano wallet address starting with `aZx`
* Your wallet's secret viewkey

Then simply select the `viewkey` option in the settings page and paste your address and viewkey. You're all set!

Note on privacy: when you validate transactions with your private viewkey, your viewkey is sent to (but not stored on) xmrchain.net over HTTPS. This could potentially allow an attacker to see your incoming, but not outgoing, transactions if they were to get his hands on your viewkey. Even if this were to happen, your funds would still be safe and it would be impossible for somebody to steal your money. For maximum privacy use your own `zano-wallet-rpc` instance.

## Option 2: Using `zano-wallet-rpc`

The most secure way to accept Zano on your website. You'll need:

* Root access to your webserver
* Latest [Zano-currency binaries](https://github.com/.....)

--- // ! TODO
After downloading (or compiling) the Zano binaries on your server, install the [systemd unit files](https://github.com/monero-integrations/monerowp/tree/master/assets/systemd-unit-files) or run `monerod` and `monero-wallet-rpc` with `screen` or `tmux`. You can skip running `monerod` by using a remote node with `monero-wallet-rpc` by adding `--daemon-address node.moneroworld.com:18089` to the `monero-wallet-rpc.service` file.
---

Note on security: using this option, while the most secure, requires you to run the Zano wallet RPC program on your server. Best practice for this is to use a view-only wallet since otherwise your server would be running a hot-wallet and a security breach could allow hackers to empty your funds.

## Configuration

* `Enable / Disable` - Turn on or off Zano gateway. (Default: Disable)
* `Title` - Name of the payment gateway as displayed to the customer. (Default: Zano Gateway)
* `Discount for using Zano` - Percentage discount applied to orders for paying with Zano. Can also be negative to apply a surcharge. (Default: 0)
* `Order valid time` - Number of seconds after order is placed that the transaction must be seen in the mempool. (Default: 3600 [1 hour])
* `Number of confirmations` - Number of confirmations the transaction must recieve before the order is marked as complete. Use `0` for nearly instant confirmation. (Default: 5)
* `Confirmation Type` - Confirm transactions with either your viewkey, or by using `zano-wallet-rpc`. (Default: viewkey)
* `Zano Address` (if confirmation type is viewkey) - Your public Zano address starting with 4. (No default)
* `Secret Viewkey` (if confirmation type is viewkey) - Your *private* viewkey (No default)
* `Zano wallet RPC Host/IP` (if confirmation type is `zano-wallet-rpc`) - IP address where the wallet rpc is running. It is highly discouraged to run the wallet anywhere other than the local server! (Default: 127.0.0.1)
* `Zano wallet RPC port` (if confirmation type is `zano-wallet-rpc`) - Port the wallet rpc is bound to with the `--rpc-bind-port` argument. (Default 18080)
* `Testnet` - Check this to change the blockchain explorer links to the testnet explorer. (Default: unchecked)
* `SSL warnings` - Check this to silence SSL warnings. (Default: unchecked)
* `Show QR Code` - Show payment QR codes. (Default: unchecked)
* `Show Prices in Zano` - Convert all prices on the frontend to Zano. Experimental feature, only use if you do not accept any other payment option. (Default: unchecked)
* `Display Decimals` (if show prices in Zano is enabled) - Number of decimals to round prices to on the frontend. The final order amount will not be rounded and will be displayed down to the nanoZano. (Default: 12)

## Shortcodes

This plugin makes available two shortcodes that you can use in your theme.

#### Live price shortcode

This will display the price of Zano in the selected currency. If no currency is provided, the store's default currency will be used.

```
[zano-price]
[zano-price currency="BTC"]
[zano-price currency="USD"]
[zano-price currency="CAD"]
[zano-price currency="EUR"]
[zano-price currency="GBP"]
```
Will display:
```
1 ZANO = 123.68000 USD
1 ZANO = 0.01827000 BTC
1 ZANO = 123.68000 USD
1 ZANO = 168.43000 CAD
1 ZANO = 105.54000 EUR
1 ZANO = 94.84000 GBP
```


#### Zano accepted here badge

This will display a badge showing that you accept Zano-currency.

`[zano-accepted-here]`

![Zano Accepted Here](/assets/images/zano-accepted-here.png?raw=true "Zano Accepted Here")


## Roadmap

* Enable Zano RPC for self-hosting
* Add ability to make payments using Zano Companion extention

## Donations

razorsyntax: ZxDskQm2MLP2BUYjf1T38fKQuFaep1MAoHZevNVWpVeZULGm54qBXTJ5YPmWMjAfNeLWnyjyf47GE55KwFGMsxsq2BCNNup5s
