<?php
/*
Plugin Name: Custom Login Design
Plugin URI: https://siteweb.es/
Description: A plugin to fully customize the WordPress login page design.
Version: 1.0
Author: Hamid Ezzaki
Author URI: https://siteweb.es/
License: GPL2
*/

// Add settings menu
function custom_login_settings_menu() {
    add_menu_page(
        'Custom Login Design',               // Page title
        'Login Design',                      // Menu title
        'manage_options',                    // Capability required
        'custom-login-design',               // Menu slug
        'custom_login_settings_page',        // Function to display settings page
        'dashicons-admin-appearance',        // Icon
        80                                   // Position
    );
}
add_action( 'admin_menu', 'custom_login_settings_menu' );

// Create the settings page
function custom_login_settings_page() {
    ?>
    <div class="wrap">
        <h1>Customize Login Page Design</h1>
        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php
            settings_fields( 'custom_login_settings_group' );
            do_settings_sections( 'custom-login-design' );
            ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row">Custom Login Logo</th>
                    <td>
                        <input type="file" name="custom_login_logo" />
                        <?php
                        $custom_logo = get_option( 'custom_login_logo' );
                        if ( ! empty( $custom_logo ) ) {
                            echo '<p>Current Logo: <img src="' . esc_url( $custom_logo ) . '" style="max-width: 200px; height: auto;" /></p>';
                        }
                        ?>
                        <p class="description">Upload a custom logo for the login page.</p>
                    </td>
                </tr>
               <tr valign="top">
                    <th scope="row">Form Background Color</th>
                    <td>
                        <input type="text" name="custom_login_form_background_color" value="<?php echo esc_attr( get_option( 'custom_login_form_background_color' ) ); ?>" class="color-picker" data-default-color="#ffffff" />
                        <p class="description">Choose background color for the login form container.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Custom Login Background Color</th>
                    <td>
                        <input type="text" name="custom_login_background_color" value="<?php echo esc_attr( get_option( 'custom_login_background_color' ) ); ?>" class="color-picker" data-default-color="#ffffff" />
                        <p class="description">Choose a background color for the login page.</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Input Field Style</th>
                    <td>
                        <select name="custom_login_input_style">
                            <option value="rounded" <?php selected( get_option( 'custom_login_input_style' ), 'rounded' ); ?>>Rounded</option>
                            <option value="square" <?php selected( get_option( 'custom_login_input_style' ), 'square' ); ?>>Square</option>
                        </select>
                        <p class="description">Choose the style of the input fields.</p>
                    </td>
                </tr>
                <tr valign="top">
                    <th scope="row">Heading Text Color</th>
                    <td>
                        <input type="text" name="custom_login_heading_color" value="<?php echo esc_attr( get_option( 'custom_login_heading_color' ) ); ?>" class="color-picker" data-default-color="#1a1a1a" />
                        <p class="description">Color for the form heading text</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Label Text Color</th>
                    <td>
                        <input type="text" name="custom_login_label_color" value="<?php echo esc_attr( get_option( 'custom_login_label_color' ) ); ?>" class="color-picker" data-default-color="#3c434a" />
                        <p class="description">Color for input labels</p>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row">Link Color</th>
                    <td>
                        <input type="text" name="custom_login_link_color" value="<?php echo esc_attr( get_option( 'custom_login_link_color' ) ); ?>" class="color-picker" data-default-color="#2271b1" />
                        <p class="description">Color for links and button</p>
                    </td>
                </tr>
            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Register settings (excluding the logo from Settings API)
function custom_login_register_settings() {
    register_setting( 'custom_login_settings_group', 'custom_login_background_color' );
    register_setting( 'custom_login_settings_group', 'custom_login_form_background_color' );
    register_setting( 'custom_login_settings_group', 'custom_login_heading_color' );
    register_setting( 'custom_login_settings_group', 'custom_login_label_color' );
    register_setting( 'custom_login_settings_group', 'custom_login_link_color' );
    register_setting( 'custom_login_settings_group', 'custom_login_input_style' );
}
add_action( 'admin_init', 'custom_login_register_settings' );

// Handle the logo file upload separately
function custom_login_handle_logo_upload() {
    if ( isset( $_POST['option_page'] ) && $_POST['option_page'] === 'custom_login_settings_group' ) {
        if ( ! empty( $_FILES['custom_login_logo']['tmp_name'] ) ) {
            require_once( ABSPATH . 'wp-admin/includes/media.php' );
            require_once( ABSPATH . 'wp-admin/includes/file.php' );
            require_once( ABSPATH . 'wp-admin/includes/image.php' );

            $upload_overrides = array( 'test_form' => false );
            $uploaded = media_handle_upload( 'custom_login_logo', 0, array(), $upload_overrides );

            if ( ! is_wp_error( $uploaded ) ) {
                $logo_url = wp_get_attachment_url( $uploaded );
                update_option( 'custom_login_logo', $logo_url );
            } else {
                error_log( 'Logo upload error: ' . $uploaded->get_error_message() );
            }
        }
    }
}
add_action( 'admin_init', 'custom_login_handle_logo_upload' );

// Apply custom login logo to the login page
// Apply custom login logo to the login page
function custom_login_logo() {
    $custom_logo = get_option( 'custom_login_logo' );
    if ( $custom_logo ) {
        echo '<style>
            .wp-login-logo a {
                background-image: url(' . esc_url( $custom_logo ) . ') !important;
                background-size: contain !important;
                height: 80px !important;
                width: 320px !important;
                background-repeat: no-repeat !important;
                display: block !important;
            }
            .wp-login-logo svg {
                display: none !important; /* Hide default WordPress logo */
            }
        </style>';
    }
}
add_action( 'login_enqueue_scripts', 'custom_login_logo' );

// Change the logo link URL
function custom_login_logo_url() {
    return home_url(); // Change to your desired URL
}
add_filter( 'login_headerurl', 'custom_login_logo_url' );

// Change the logo link title
function custom_login_logo_title() {
    return get_bloginfo('name'); // Change to your site's name
}
add_filter( 'login_headertext', 'custom_login_logo_title' );

// Update custom_login_styles function:
function custom_login_styles() {
    // Get all color options
    $colors = [
        'heading' => get_option( 'custom_login_heading_color' ),
        'label' => get_option( 'custom_login_label_color' ),
        'link' => get_option( 'custom_login_link_color' ),
        'form_bg' => get_option( 'custom_login_form_background_color' ),
        'page_bg' => get_option( 'custom_login_background_color' )
    ];

    // Page background
    if ($colors['page_bg']) {
        echo '<style>body.login { background-color: ' . esc_attr($colors['page_bg']) . '; }</style>';
    }

    // Form container
    if ($colors['form_bg']) {
        echo '<style>
            #loginform {
                background: ' . esc_attr($colors['form_bg']) . ' !important;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                padding: 30px !important;
            }
        </style>';
    }

    // Text colors
    echo '<style>';
    if ($colors['heading']) {
        echo '#loginform .login-heading { color: ' . esc_attr($colors['heading']) . ' !important; }';
    }
    if ($colors['label']) {
        echo '#loginform label { color: ' . esc_attr($colors['label']) . ' !important; }';
    }
    if ($colors['link']) {
        echo '
            #loginform a,
            #loginform .button.wp-core-ui {
                color: ' . esc_attr($colors['link']) . ' !important;
            }
            #loginform .button.wp-core-ui {
                border-color: ' . esc_attr($colors['link']) . ' !important;
                background-color: transparent !important;
            }
            #loginform .button.wp-core-ui:hover {
                background-color: ' . esc_attr($colors['link']) . ' !important;
                color: #fff !important;
            }
        ';
    }
    echo '</style>';

    // Input styles
    $input_radius = (get_option('custom_login_input_style') === 'square') ? '0' : '5px';
    echo '<style>
        .login input[type="text"], 
        .login input[type="password"] {
            border-radius: ' . $input_radius . ' !important;
        }
    </style>';
}
add_action( 'login_enqueue_scripts', 'custom_login_styles' );

// Enqueue color picker scripts
function custom_login_admin_scripts($hook) {
    if ( 'toplevel_page_custom-login-design' !== $hook ) {
        return;
    }
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wp-color-picker' );
    wp_add_inline_script( 'wp-color-picker', '
        jQuery(document).ready(function($){
            $(".color-picker").wpColorPicker();
        });
    ' );
}
add_action( 'admin_enqueue_scripts', 'custom_login_admin_scripts' );
