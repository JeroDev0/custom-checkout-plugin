<?php
/*
Plugin Name: Custom Checkout Plugin
Description: Agrega un campo de checkbox al checkout para enviar datos a Google Sheets.
*/

// Agregar campo de checkbox al checkout
add_action('woocommerce_checkout_before_order_review', 'custom_checkout_checkbox');
function custom_checkout_checkbox() {
    echo '<div id="custom_checkout_checkbox">';
    woocommerce_form_field('custom_checkbox', array(
        'type' => 'checkbox',
        'class' => array('input-checkbox'),
        'label' => __('¿Deseas recibir los mensajes en ingles', 'custom_checkout_checkbox'),
    ));
    echo '</div>';
}

// Enviar datos a Google Sheets cuando se completa la orden
add_action('woocommerce_order_status_completed', 'send_data_to_google_sheets');
function send_data_to_google_sheets($order_id) {
    $custom_checkbox = isset($_POST['custom_checkbox']) ? 'Marcado' : 'No marcado';

    // Aquí deberás usar tu lógica para enviar los datos al Google Sheet
    // Utiliza $custom_checkbox en tus datos para enviar a la hoja de cálculo.
}

// Agregar el enlace a la página de configuración en el menú de Vapelabs
add_action('admin_menu', 'custom_checkout_plugin_menu');
function custom_checkout_plugin_menu() {
    add_menu_page(
        'VapeLab',
        'VapeLab',
        'manage_options',
        'vapelab-menu-page',
        '',
        plugins_url('assets/images/icon.png', dirname(__DIR__)),
        25
    );

    add_submenu_page(
        'vapelab-menu-page',
        'Custom Checkout Plugin',
        'Custom Checkout Plugin',
        'manage_options',
        'custom-checkout-plugin-settings',
        'custom_checkout_plugin_settings_page'
    );

    //remove_submenu_page('vapelab-menu-page', 'vapelab-menu-page');
}

// Función para la página de configuración
function custom_checkout_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h2>Configuración del Plugin</h2>
        <form method="post" enctype="multipart/form-data" action="<?php echo admin_url('admin-post.php'); ?>">
            <input type="hidden" name="action" value="custom_checkout_plugin_save_settings">
            <?php wp_nonce_field('custom_checkout_plugin_nonce', 'custom_checkout_plugin_nonce'); ?>
            <input type="file" name="credentials_file" accept=".json" />
            <?php submit_button('Guardar configuración'); ?>
        </form>
    </div>
    <?php
}

// Función para procesar la carga del archivo
add_action('admin_post_custom_checkout_plugin_save_settings', 'custom_checkout_plugin_save_settings');
function custom_checkout_plugin_save_settings() {
    if (!isset($_POST['custom_checkout_plugin_nonce']) || !wp_verify_nonce($_POST['custom_checkout_plugin_nonce'], 'custom_checkout_plugin_nonce')) {
        wp_die('Acceso no autorizado');
    }

    if (isset($_FILES['credentials_file']) && $_FILES['credentials_file']['error'] === UPLOAD_ERR_OK) {
        $uploaded_file = $_FILES['credentials_file'];
        $file_path = $uploaded_file['tmp_name'];

        $credentials = file_get_contents($file_path);
        update_option('custom_checkout_plugin_credentials', $credentials);

        wp_redirect(add_query_arg('updated', 'true', $_POST['_wp_http_referer']));
        exit();
    }
}
?>
