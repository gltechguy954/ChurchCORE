<?php
/**
 * Front-end shortcodes and portal UI.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Registers shortcodes for self-serve portal.
 */
class ChurchCore_Shortcodes {
    /**
     * Registers hooks.
     */
    public function register() {
        add_shortcode( 'churchcore_portal', array( $this, 'render_portal' ) );
    }

    /**
     * Outputs the portal interface.
     *
     * @param array $atts Shortcode attributes.
     * @return string
     */
    public function render_portal( $atts ) {
        if ( ! is_user_logged_in() ) {
            return wp_kses_post( __( 'You must be logged in to view the ChurchCore portal.', 'churchcore' ) );
        }

        ob_start();
        include CHURCHCORE_PLUGIN_DIR . 'includes/frontend/portal.php';
        return ob_get_clean();
    }
}
