<?php foreach($errors as $error): ?>
<div class="error"><p><strong>Zano Gateway Error</strong>: <?php echo $error; ?></p></div>
<?php endforeach; ?>

<h1>Zano Gateway Settings</h1>

<?php if($confirm_type === 'zano-wallet-rpc'): ?>
<div style="border:1px solid #ddd;padding:5px 10px;">
    <?php
         echo 'Wallet height: ' . $balance['height'] . '</br>';
         echo 'Your balance is: ' . $balance['balance'] . '</br>';
         echo 'Unlocked balance: ' . $balance['unlocked_balance'] . '</br>';
         ?>
</div>
<?php endif; ?>

<table class="form-table">
    <?php echo $settings_html ?>
</table>

<h4><a href="https://github.com/.....">Learn more about using the Zano payment gateway</a></h4>

<script>
function zanoUpdateFields() {
    var confirmType = jQuery("#woocommerce_zano_gateway_confirm_type").val();
    if(confirmType == "zano-wallet-rpc") {
        jQuery("#woocommerce_zano_gateway_zano_address").closest("tr").hide();
        jQuery("#woocommerce_zano_gateway_viewkey").closest("tr").hide();
        jQuery("#woocommerce_zano_gateway_daemon_host").closest("tr").show();
        jQuery("#woocommerce_zano_gateway_daemon_port").closest("tr").show();
        jQuery("#woocommerce_zano_gateway_daemon_host_port").closest("tr").show();
        
    } else {
        jQuery("#woocommerce_zano_gateway_zano_address").closest("tr").show();
        jQuery("#woocommerce_zano_gateway_viewkey").closest("tr").show();
        jQuery("#woocommerce_zano_gateway_daemon_host").closest("tr").hide();
        jQuery("#woocommerce_zano_gateway_daemon_port").closest("tr").hide();
        jQuery("#woocommerce_zano_gateway_daemon_host_port").closest("tr").hide();
    }
    var useZanoPrices = jQuery("#woocommerce_zano_gateway_use_zano_price").is(":checked");
    if(useZanoPrices) {
        jQuery("#woocommerce_zano_gateway_use_zano_price_decimals").closest("tr").show();
    } else {
        jQuery("#woocommerce_zano_gateway_use_zano_price_decimals").closest("tr").hide();
    }
}
zanoUpdateFields();
jQuery("#woocommerce_zano_gateway_confirm_type").change(zanoUpdateFields);
jQuery("#woocommerce_zano_gateway_use_zano_price").change(zanoUpdateFields);
</script>

<style>
#woocommerce_zano_gateway_zano_address,
#woocommerce_zano_gateway_viewkey {
    width: 100%;
}
</style>