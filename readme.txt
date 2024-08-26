=== Zano WooCommerce Extension ===
Originally developed by serhack, mosu-forge and Monero Integrations for Monero WooCommerce Gateway plugin
Contributor: razorsyntax
Donate link: http://.....
Tags: zano, woocommerce, integration, payment, merchant, cryptocurrency, accept zano, zano woocommerce
Requires at least: 4.0
Tested up to: 5.7.2
Stable tag: trunk
License: MIT license
License URI: https://github.com/.....
 
Zano WooCommerce Extension is a Wordpress plugin that allows to accept Zano at WooCommerce-powered online stores.

= Benefits =

* Payment validation done through either `zano-wallet-rpc` or the [explorer.zano.org blockchain explorer](https://explorer.zano.org/).
* Validates payments with `cron`, so does not require users to stay on the order confirmation page for their order to validate.
* Order status updates are done through AJAX instead of Javascript page reloads.
* Customers can pay with multiple transactions and are notified as soon as transactions hit the mempool.
* Configurable block confirmations, from `0` for zero confirm to `60` for high ticket purchases.
* Live price updates every minute; total amount due is locked in after the order is placed for a configurable amount of time (default 60 minutes) so the price does not change after order has been made.
* Hooks into emails, order confirmation page, customer order history page, and admin order details page.
* View all payments received to your wallet with links to the blockchain explorer and associated orders.
* Optionally display all prices on your store in terms of Zano.
* Shortcodes! Display exchange rates in numerous currencies.

= Installation =

== Automatic method ==

In the "Add Plugins" section of the WordPress admin UI, search for "zano" and click the Install Now button next to "Zano WooCommerce Extension" by mosu-forge, SerHack.  This will enable auto-updates, but only for official releases, so if you need to work from git master or your local fork, please use the manual method below.

== Manual method == 

* Download the plugin from the releases page (https://github.com/.....) or clone with `git clone https://github.com/.....`
* Unzip or place the `zano-woocommerce-gateway` folder in the `wp-content/plugins` directory.
* Activate "Zano Woocommerce Gateway" in your WordPress admin dashboard.
* It is highly recommended that you use native cronjobs instead of WordPress's "Poor Man's Cron" by adding `define('DISABLE_WP_CRON', true);` into your `wp-config.php` file and adding `* * * * * wget -q -O - https://yourstore.com/wp-cron.php?doing_wp_cron >/dev/null 2>&1` to your crontab.

= Configuration =

== Option 1: Use your wallet address and viewkey ==

This is the easiest way to start accepting Zano on your website. You'll need:

* Your Zano wallet address # --starting with `aZx`--
* Your wallet's secret viewkey

Then simply select the `viewkey` option in the settings page and paste your address and viewkey. You're all set!

Note on privacy: when you validate transactions with your private viewkey, your viewkey is sent to (but not stored on) xmrchain.net over HTTPS. This could potentially allow an attacker to see your incoming, but not outgoing, transactions if they were to get his hands on your viewkey. Even if this were to happen, your funds would still be safe and it would be impossible for somebody to steal your money. For maximum privacy use your own `zano-wallet-rpc` instance.

== Option 2: Using Zano wallet rpc ==

The most secure way to accept Zano on your website. You'll need:

* Root access to your webserver
* Latest [Zano-currency binaries](https://github.com/.....)

// ! TODO
After downloading (or compiling) the Zano binaries on your server, install the [systemd unit files](https://github.com/...../tree/master/assets/systemd-unit-files) or run `zanod` and `zano-wallet-rpc` with `screen` or `tmux`. You can skip running `zanod` by using a remote node with `zano-wallet-rpc` by adding `--daemon-address node.moneroworld.com:18089` to the `zano-wallet-rpc.service` file.

Note on security: using this option, while the most secure, requires you to run the Zano wallet RPC program on your server. Best practice for this is to use a view-only wallet since otherwise your server would be running a hot-wallet and a security breach could allow hackers to empty your funds.

== Remove plugin ==

1. Deactivate plugin through the 'Plugins' menu in WordPress
2. Delete plugin through the 'Plugins' menu in WordPress

== Screenshots == 
1. Zano Payment Box
2. Zano Options

== Changelog ==

= 0.1 =
* First version ! Yay!

= 1.0 =
* Added the view key option

= 2.1 =
* Verify transactions without zano-wallet-rpc
* Optionally accept zero confirmation transactions
* bug fixing

= 2.2 =
* Fix some bugs

= 2.3 =
* Bug fixing

= 3.0.0 =
Huge shoutout to mosu-forge who contributed a lot to make 3.0 possible.
* Ability to set number of confirms: 0 for zero conf, up to 60.
* Amount owed in XMR gets locked in after the order for a configurable amount of time after which the order is invalid, default 60 minutes.
* Shows transactions received along with the number of confirms right on the order success page, auto-updates through AJAX.
* QR code generation is done with Javascript instead of sending payment details to a 3rd party.
* Admin page for showing all transactions made to the wallet.
* Logic is done via cron, instead of the user having to stay on the order page until payment is confirmed.
* Payment details (along with the txid) are always visible on the customer's account dashboard on the my orders section.
* Live prices are also run via cron, shortcodes for showing exchange rates.
* Properly hooks into order confirmation email page.

= 3.0.1 =
* Fixed the incorrect generation of integrated addresses;

= 3.0.2 =
* Fixed the problem of 'hard-coded' prices which causes a division by zero: now any currencies supported by cryptocompare API should work;

= 3.0.3 =
* Fixed the problem related to explorer;

= 3.0.4 =
* Bug fixing;

= 3.0.5 =
* Removed cryptocompare.com API and switched to CoinGecko

== Upgrade Notice ==

soon

== Frequently Asked Questions ==

* What is Zano ?
Zano is completely private, cryptographically secure, digital cash used across the globe. See https://zano.org for more information

* What is a Zano wallet?
A Zano wallet is a piece of software that allows you to store your funds and interact with the Zano network. You can get a Zano wallet from https://.....

* What is zano-wallet-rpc ?
The zano-wallet-rpc is an RPC server that will allow this plugin to communicate with the Zano network. You can download it from https://..... with the command-line tools.

* Why do I see `[ERROR] Failed to connect to zano-wallet-rpc at localhost port 18080
Syntax error: Invalid response data structure: Request id: 1 is different from Response id: ` ?
This is most likely because this plugin can not reach your zano-wallet-rpc. Make sure that you have supplied the correct host IP and port to the plugin in their fields. If your zano-wallet-rpc is on a different server than your wordpress site, make sure that the appropriate port is open with port forwarding enabled.
