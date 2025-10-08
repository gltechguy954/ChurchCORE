<?php
/**
 * Integrations with other Fluent and WooCommerce products.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Detects and surfaces integration points.
 */
class ChurchCore_Integrations {
    /**
     * Cached integration statuses.
     *
     * @var array
     */
    protected $status_cache;

    /**
     * Returns integration availability.
     *
     * @return array
     */
    public function get_status() {
        if ( null !== $this->status_cache ) {
            return $this->status_cache;
        }

        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        $this->status_cache = array(
            'fluentcrm'    => is_plugin_active( 'fluent-crm/fluent-crm.php' ),
            'fluentforms'  => is_plugin_active( 'fluentform/fluentform.php' ),
            'fluentboards' => is_plugin_active( 'fluent-boards/fluent-boards.php' ),
            'woocommerce'  => is_plugin_active( 'woocommerce/woocommerce.php' ),
        );

        return $this->status_cache;
    }

    /**
     * Outputs admin notices when integrations are missing.
     */
    public function render_notices() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $status = $this->get_status();

        foreach ( $status as $integration => $active ) {
            if ( $active ) {
                continue;
            }

            $plugin_name = $this->get_integration_label( $integration );
            printf(
                '<div class="notice notice-warning is-dismissible"><p>%s</p></div>',
                esc_html( sprintf( __( '%s is not active. Activate it to unlock deeper ChurchCore sync.', 'churchcore' ), $plugin_name ) )
            );
        }
    }

    /**
     * Returns a human-readable label.
     *
     * @param string $integration Integration slug.
     * @return string
     */
    protected function get_integration_label( $integration ) {
        $labels = array(
            'fluentcrm'    => __( 'FluentCRM', 'churchcore' ),
            'fluentforms'  => __( 'Fluent Forms', 'churchcore' ),
            'fluentboards' => __( 'Fluent Boards', 'churchcore' ),
            'woocommerce'  => __( 'WooCommerce', 'churchcore' ),
        );

        return isset( $labels[ $integration ] ) ? $labels[ $integration ] : $integration;
    }
}
