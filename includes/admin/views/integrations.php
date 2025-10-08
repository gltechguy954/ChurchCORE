<?php
/**
 * Integrations overview screen.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$integration = new ChurchCore_Integrations();
$status      = $integration->get_status();
?>
<div class="wrap churchcore-wrap">
    <h1><?php esc_html_e( 'Integrations', 'churchcore' ); ?></h1>
    <p><?php esc_html_e( 'Connect your existing Fluent apps and WooCommerce store to surface giving, communication, and volunteer data in one place.', 'churchcore' ); ?></p>
    <div class="churchcore-grid">
        <?php foreach ( $status as $slug => $active ) : ?>
            <div class="churchcore-card">
                <h2><?php echo esc_html( ucfirst( $slug ) ); ?></h2>
                <p>
                    <?php
                    if ( $active ) {
                        esc_html_e( 'Active and syncing.', 'churchcore' );
                    } else {
                        esc_html_e( 'Not detected. Activate the plugin to enable syncing.', 'churchcore' );
                    }
                    ?>
                </p>
                <?php if ( ! $active ) : ?>
                    <p><a class="button button-primary" href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>"><?php esc_html_e( 'Manage Plugins', 'churchcore' ); ?></a></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="churchcore-card">
        <h2><?php esc_html_e( 'Automation Recipes', 'churchcore' ); ?></h2>
        <p><?php esc_html_e( 'Use FluentCRM and Fluent Forms to build follow-up workflows for new visitors, volunteers, and donors. ChurchCore exposes key merge tags to personalize automations.', 'churchcore' ); ?></p>
        <ul>
            <li><?php esc_html_e( 'Send a welcome email when a new family is added.', 'churchcore' ); ?></li>
            <li><?php esc_html_e( 'Create a Fluent Board card when a pastoral care note is logged.', 'churchcore' ); ?></li>
            <li><?php esc_html_e( 'Tag WooCommerce donors when they complete a purchase.', 'churchcore' ); ?></li>
        </ul>
    </div>
</div>
