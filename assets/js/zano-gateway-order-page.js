/*
 * Copyright (c) 2018, Ryo Currency Project
*/
function zano_showNotification(message, type='success') {
    var toast = jQuery('<div class="' + type + '"><span>' + message + '</span></div>');
    jQuery('#zano_toast').append(toast);
    toast.animate({ "right": "12px" }, "fast");
    setInterval(function() {
        toast.animate({ "right": "-400px" }, "fast", function() {
            toast.remove();
        });
    }, 2500)
}
function zano_showQR(show=true) {
    jQuery('#zano_qr_code_container').toggle(show);
}
function zano_fetchDetails() {
    var data = {
        '_': jQuery.now(),
        'order_id': zano_details.order_id
    };
    jQuery.get(zano_ajax_url, data, function(response) {
        if (typeof response.error !== 'undefined') {
            console.log(response.error);
        } else {
            zano_details = response;
            zano_updateDetails();
        }
    });
}

function zano_updateDetails() {

    var details = zano_details;

    jQuery('#zano_payment_messages').children().hide();
    switch(details.status) {
        case 'unpaid':
            jQuery('.zano_payment_unpaid').show();
            jQuery('.zano_payment_expire_time').html(details.order_expires);
            break;
        case 'partial':
            jQuery('.zano_payment_partial').show();
            jQuery('.zano_payment_expire_time').html(details.order_expires);
            break;
        case 'paid':
            jQuery('.zano_payment_paid').show();
            jQuery('.zano_confirm_time').html(details.time_to_confirm);
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'confirmed':
            jQuery('.zano_payment_confirmed').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired':
            jQuery('.zano_payment_expired').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
        case 'expired_partial':
            jQuery('.zano_payment_expired_partial').show();
            jQuery('.button-row button').prop("disabled",true);
            break;
    }

    jQuery('#zano_exchange_rate').html('1 ZANO = '+details.rate_formatted+' '+details.currency);
    jQuery('#zano_total_amount').html(details.amount_total_formatted);
    jQuery('#zano_total_paid').html(details.amount_paid_formatted);
    jQuery('#zano_total_due').html(details.amount_due_formatted);

    jQuery('#zano_integrated_address').html(details.integrated_address);

    if(zano_show_qr) {
        var qr = jQuery('#zano_qr_code').html('');
        new QRCode(qr.get(0), details.qrcode_uri);
    }

    if(details.txs.length) {
        jQuery('#zano_tx_table').show();
        jQuery('#zano_tx_none').hide();
        jQuery('#zano_tx_table tbody').html('');
        for(var i=0; i < details.txs.length; i++) {
            var tx = details.txs[i];
            var height = tx.height == 0 ? 'N/A' : tx.height;
            var row = ''+
                '<tr>'+
                '<td style="word-break: break-all">'+
                '<a href="'+zano_explorer_url+'/transaction/'+tx.txid+'" target="_blank">'+tx.txid+'</a>'+
                '</td>'+
                '<td>'+height+'</td>'+
                '<td>'+tx.amount_formatted+' Zano</td>'+
                '</tr>';

            jQuery('#zano_tx_table tbody').append(row);
        }
    } else {
        jQuery('#zano_tx_table').hide();
        jQuery('#zano_tx_none').show();
    }

    // Show state change notifications
    var new_txs = details.txs;
    var old_txs = zano_order_state.txs;
    if(new_txs.length != old_txs.length) {
        for(var i = 0; i < new_txs.length; i++) {
            var is_new_tx = true;
            for(var j = 0; j < old_txs.length; j++) {
                if(new_txs[i].txid == old_txs[j].txid && new_txs[i].amount == old_txs[j].amount) {
                    is_new_tx = false;
                    break;
                }
            }
            if(is_new_tx) {
                zano_showNotification('Transaction received for '+new_txs[i].amount_formatted+' Zano');
            }
        }
    }

    if(details.status != zano_order_state.status) {
        switch(details.status) {
            case 'paid':
                zano_showNotification('Your order has been paid in full');
                break;
            case 'confirmed':
                zano_showNotification('Your order has been confirmed');
                break;
            case 'expired':
            case 'expired_partial':
                zano_showNotification('Your order has expired', 'error');
                break;
        }
    }

    zano_order_state = {
        status: zano_details.status,
        txs: zano_details.txs
    };

}
jQuery(document).ready(function($) {
    if (typeof zano_details !== 'undefined') {
        zano_order_state = {
            status: zano_details.status,
            txs: zano_details.txs
        };
        setInterval(zano_fetchDetails, 30000);
        zano_updateDetails();
        new ClipboardJS('.clipboard').on('success', function(e) {
            e.clearSelection();
            if(e.trigger.disabled) return;
            switch(e.trigger.getAttribute('data-clipboard-target')) {
                case '#zano_integrated_address':
                    zano_showNotification('Copied destination address!');
                    break;
                case '#zano_total_due':
                    zano_showNotification('Copied total amount due!');
                    break;
            }
            e.clearSelection();
        });
    }
});