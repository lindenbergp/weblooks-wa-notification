<?php
/*
Plugin Name: Weblooks WA Notification
Plugin URI: https://github.com/lindenbergp/weblooks-wa-notification
Description: Envia uma notificação SMS para o cliente quando o status do pedido é atualizado.
Version: 1.1.2
Author: Weblooks
Author URI: https://weblooks.com.br
License: GPL2
License URI: https://github.com/lindenbergp/weblooks-wa-notification/blob/main/LICENSE
*/

function weblooks_sms_settings_init() {
    add_settings_section(
        'weblooks_sms_settings',
        'Configurações de SMS Weblooks',
        'weblooks_sms_settings_callback',
        'general'
    );
    add_settings_field(
        'weblooks_sms_sessionkey',
        'Sessionkey',
        'weblooks_sms_sessionkey_callback',
        'general',
        'weblooks_sms_settings'
    );
    add_settings_field(
        'weblooks_sms_session',
        'Nome da sessão',
        'weblooks_sms_session_callback',
        'general',
        'weblooks_sms_settings'
    );
    add_settings_field(
        'weblooks_sms_default_message',
        'Mensagem de SMS padrão',
        'weblooks_sms_default_message_callback',
        'general',
        'weblooks_sms_settings'
    );
    add_settings_field(
        'weblooks_sms_pending_message',
        'Mensagem de SMS pendente',
        'weblooks_sms_pending_message_callback',
        'general',
        'weblooks_sms_settings'
    );
    add_settings_field(
        'weblooks_sms_processing_message',
        'Mensagem de SMS processando',
        'weblooks_sms_processing_message_callback',
        'general',
        'weblooks_sms_settings'
    );
    add_settings_field(
        'weblooks_sms_completed_message',
        'Mensagem de SMS concluído',
        'weblooks_sms_completed_message_callback',
        'general',
        'weblooks_sms_settings'
    );
    add_settings_field(
        'weblooks_sms_cancelled_message',
        'Mensagem de SMS Cancelado',
        'weblooks_sms_cancelled_message_callback',
        'general',
        'weblooks_sms_settings'
    );
    register_setting( 'general', 'weblooks_sms_sessionkey' );
    register_setting( 'general', 'weblooks_sms_session' );
    register_setting( 'general', 'weblooks_sms_default_message' );
    register_setting( 'general', 'weblooks_sms_pending_message' );
    register_setting( 'general', 'weblooks_sms_processing_message' );
    register_setting( 'general', 'weblooks_sms_completed_message' );
    register_setting( 'general', 'weblooks_sms_cancelled_message' );
}
add_action( 'admin_init', 'weblooks_sms_settings_init' );

function weblooks_sms_settings_callback() {
    echo '<p>Insira suas credenciais da Weblooks SMS e personalize as mensagens de SMS enviadas aos clientes.</p>';
    echo '<p>Shortcodes disponíveis: [customer_name], [order_id], [order_status], [payment_method], [order_items], [order_total]</p>';
}

function weblooks_sms_sessionkey_callback() {
    $sessionkey = get_option( 'weblooks_sms_sessionkey' );
    echo '<input type="text" name="weblooks_sms_sessionkey" value="' . $sessionkey . '" />';
}

function weblooks_sms_session_callback() {
    $session = get_option( 'weblooks_sms_session' );
    echo '<input type="text" name="weblooks_sms_session" value="' . $session . '" />';
}

function weblooks_sms_default_message_callback() {
    $default_message = get_option( 'weblooks_sms_default_message' );
    echo '<textarea name="weblooks_sms_default_message">' . $default_message . '</textarea>';
    echo '<p>Use shortcodes [customer_name], [order_id], [order_status], [payment_method], [order_items], [order_total] para incluir os dados do cliente e do pedido na mensagem</p>';
}

function weblooks_sms_pending_message_callback() {
    $pending_message = get_option( 'weblooks_sms_pending_message' );
    echo '<textarea name="weblooks_sms_pending_message">' . $pending_message . '</textarea>';
    echo '<p>Use shortcodes [customer_name], [order_id], [order_status], [payment_method], [order_items], [order_total] para incluir os dados do cliente e do pedido na mensagem</p>';
}

function weblooks_sms_processing_message_callback() {
    $processing_message = get_option( 'weblooks_sms_processing_message' );
    echo '<textarea name="weblooks_sms_processing_message">' . $processing_message . '</textarea>';
    echo '<p>Use shortcodes [customer_name], [order_id], [order_status], [payment_method], [order_items], [order_total] para incluir os dados do cliente e do pedido na mensagem</p>';
}

function weblooks_sms_completed_message_callback() {
    $completed_message = get_option( 'weblooks_sms_completed_message' );
    echo '<textarea name="weblooks_sms_completed_message">' . $completed_message . '</textarea>';
    echo '<p>Use shortcodes [customer_name], [order_id], [order_status], [payment_method], [order_items], [order_total] para incluir os dados do cliente e do pedido na mensagem</p>';
}
function weblooks_sms_cancelled_message_callback() {
    $completed_message = get_option( 'weblooks_sms_cancelled_message' );
    echo '<textarea name="weblooks_sms_cancelled_message">' . $cancelled_message . '</textarea>';
    echo '<p>Use shortcodes [customer_name], [order_id], [order_status], [payment_method], [order_items], [order_total] para incluir os dados do cliente e do pedido na mensagem</p>';
}
function send_sms_on_order_status_change( $order_id, $old_status, $new_status ) {
    $sessionkey = get_option( 'weblooks_sms_sessionkey' );
    $session = get_option( 'weblooks_sms_session' );
    $order = wc_get_order( $order_id );
    $customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
    $order_status = wc_get_order_status_name( $new_status );
    $order_items = "";
      foreach( $order->get_items() as $order_item ) {
    $order_items .= $order_item['name'] . " x " . $order_item['quantity'] . "\n";
    }
    $payment_method = $order->get_payment_method_title();
    $order_total = $order->get_total();
    $message = '';

    if ( $new_status === 'completed' ) {
        $message = get_option( 'weblooks_sms_completed_message' );
    } elseif ( $new_status === 'processing' ) {
        $message = get_option( 'weblooks_sms_processing_message' );
    } elseif ( $new_status === 'pending' ) {
        $message = get_option( 'weblooks_sms_pending_message' );
    } elseif ( $new_status === 'cancelled' ) {
        $message = get_option( 'weblooks_sms_cancelled_message' );
    }else {
        $message = get_option( 'weblooks_sms_default_message' );
    }

    $message = str_replace( '[customer_name]', $customer_name, $message );
    $message = str_replace( '[order_id]', $order_id, $message );
    $message = str_replace( '[order_status]', $order_status, $message );
    $message = str_replace( '[payment_method]', $payment_method, $message );
    $message = str_replace( '[order_items]', $order_items, $message );
    $message = str_replace( '[order_total]', $order_total, $message );
    $telefone = preg_replace("/[^0-9]/", "", $order->get_billing_phone());
    $telefone = "55" . $telefone;
    $api_url = "https://api.weblooks.com.br/sendText";

    $headers = array(
        'sessionkey' => $sessionkey,
        'Content-Type' => 'application/json'
    );
    $data = array(
        "session" => $session,
        "number" => $telefone,
        "text" => $message
    );
    $options = array(
        'headers' => $headers,
        'body' => json_encode($data),
        'timeout' => 60
    );
    $response = wp_remote_post( $api_url, $options );
    if ( is_wp_error( $response ) ) {
        $error_message = $response->get_error_message();
        // Adicione aqui o código para tratar o erro, como exibir uma mensagem de erro no painel do administrador
    } else {
        $response_code = wp_remote_retrieve_response_code( $response );
        if ( $response_code != '200' ) {
            // Adicione aqui o código para tratar o erro, como exibir uma mensagem de erro no painel do administrador
        }
    }
}
add_action( 'woocommerce_order_status_changed', 'send_sms_on_order_status_change', 10, 3 );

