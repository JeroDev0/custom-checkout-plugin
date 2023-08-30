<?php
// Procesar la carga del archivo cuando se envíe el formulario
add_action('admin_post_custom_checkout_plugin_save_settings', 'custom_checkout_plugin_save_settings');
function custom_checkout_plugin_save_settings() {
    // Verificar el nonce
    if (!isset($_POST['custom_checkout_plugin_nonce']) || !wp_verify_nonce($_POST['custom_checkout_plugin_nonce'], 'custom_checkout_plugin_nonce')) {
        wp_die('Acceso no autorizado');
    }

    if (isset($_FILES['credentials_file']) && $_FILES['credentials_file']['error'] === UPLOAD_ERR_OK) {
        $uploaded_file = $_FILES['credentials_file'];
        $file_path = $uploaded_file['tmp_name'];

        // Leer el contenido del archivo y guardarlo en la configuración
        $credentials = file_get_contents($file_path);
        update_option('custom_checkout_plugin_credentials', $credentials);

        // Redirigir después de guardar
        wp_redirect(add_query_arg('updated', 'true', $_POST['_wp_http_referer']));
        exit();
    }
}