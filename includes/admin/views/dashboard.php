<?php
/**
 * Dashboard overview.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$fam_count    = wp_count_posts( 'cc_family' );
$people_count = wp_count_posts( 'cc_person' );

$families_total = isset( $fam_count->publish ) ? (int) $fam_count->publish : 0;
$people_total   = isset( $people_count->publish ) ? (int) $people_count->publish : 0;

$latest_families = get_posts(
    array(
        'post_type'      => 'cc_family',
        'posts_per_page' => 5,
        'post_status'    => 'any',
    )
);

$latest_people = get_posts(
    array(
        'post_type'      => 'cc_person',
        'posts_per_page' => 5,
        'post_status'    => 'any',
    )
);
?>
<div class="wrap churchcore-wrap">
    <h1><?php esc_html_e( 'ChurchCore Overview', 'churchcore' ); ?></h1>
    <?php settings_errors( 'churchcore' ); ?>
    <div class="churchcore-metrics">
        <div class="churchcore-metric">
            <strong><?php esc_html_e( 'Families', 'churchcore' ); ?></strong>
            <div><?php echo esc_html( $families_total ); ?></div>
        </div>
        <div class="churchcore-metric">
            <strong><?php esc_html_e( 'People', 'churchcore' ); ?></strong>
            <div><?php echo esc_html( $people_total ); ?></div>
        </div>
        <div class="churchcore-metric">
            <strong><?php esc_html_e( 'Connected Apps', 'churchcore' ); ?></strong>
            <div><?php echo esc_html( count( array_filter( ( new ChurchCore_Integrations() )->get_status() ) ) ); ?></div>
        </div>
    </div>
    <div class="churchcore-grid">
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Recent Families', 'churchcore' ); ?></h2>
            <table class="churchcore-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Family', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'churchcore' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $latest_families as $family ) : ?>
                    <?php
                    $status_obj   = get_post_status_object( $family->post_status );
                    $status_label = $status_obj ? $status_obj->label : $family->post_status;
                    ?>
                    <tr>
                        <td><a href="<?php echo esc_url( get_edit_post_link( $family->ID ) ); ?>"><?php echo esc_html( get_the_title( $family ) ); ?></a></td>
                        <td><?php echo esc_html( $status_label ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Recent People', 'churchcore' ); ?></h2>
            <table class="churchcore-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'churchcore' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $latest_people as $person ) : ?>
                    <tr>
                        <td><a href="<?php echo esc_url( get_edit_post_link( $person->ID ) ); ?>"><?php echo esc_html( get_the_title( $person ) ); ?></a></td>
                        <td><?php echo esc_html( get_post_meta( $person->ID, '_churchcore_email', true ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
