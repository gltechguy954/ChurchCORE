<?php
/**
 * Front-end portal renderer.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$current_user = wp_get_current_user();
$family_posts = get_posts(
    array(
        'post_type'      => 'cc_family',
        'posts_per_page' => -1,
        'author'         => $current_user->ID,
        'post_status'    => array( 'publish', 'draft', 'pending', 'follow-up' ),
    )
);

$person_posts = get_posts(
    array(
        'post_type'      => 'cc_person',
        'posts_per_page' => -1,
        'post_status'    => array( 'publish', 'draft', 'pending', 'follow-up' ),
        'meta_query'     => array(
            array(
                'key'   => '_churchcore_email',
                'value' => $current_user->user_email,
            ),
        ),
    )
);
?>
<div class="churchcore-portal">
    <h2><?php esc_html_e( 'Welcome to ChurchCore', 'churchcore' ); ?></h2>
    <p><?php echo esc_html( sprintf( __( 'Hi %s! Here is the latest from your church profile.', 'churchcore' ), $current_user->display_name ) ); ?></p>
    <section class="churchcore-portal-section">
        <h3><?php esc_html_e( 'Your Family Records', 'churchcore' ); ?></h3>
        <?php if ( $family_posts ) : ?>
            <ul>
                <?php foreach ( $family_posts as $family ) :
                    $status_obj   = get_post_status_object( $family->post_status );
                    $status_label = $status_obj ? $status_obj->label : $family->post_status;
                    ?>
                    <li>
                        <strong><?php echo esc_html( get_the_title( $family ) ); ?></strong><br />
                        <span class="status"><?php echo esc_html( $status_label ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><?php esc_html_e( 'No family records assigned yet.', 'churchcore' ); ?></p>
        <?php endif; ?>
    </section>
    <section class="churchcore-portal-section">
        <h3><?php esc_html_e( 'Your Involvement', 'churchcore' ); ?></h3>
        <?php if ( $person_posts ) : ?>
            <ul>
                <?php foreach ( $person_posts as $person ) :
                    $roles = wp_get_post_terms( $person->ID, 'cc_role', array( 'fields' => 'names' ) );
                    ?>
                    <li>
                        <strong><?php echo esc_html( get_the_title( $person ) ); ?></strong><br />
                        <span><?php echo esc_html( $roles ? implode( ', ', $roles ) : __( 'No roles assigned', 'churchcore' ) ); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><?php esc_html_e( 'No volunteer or staff assignments found.', 'churchcore' ); ?></p>
        <?php endif; ?>
    </section>
    <section class="churchcore-portal-section">
        <h3><?php esc_html_e( 'Need to update something?', 'churchcore' ); ?></h3>
        <p><?php esc_html_e( 'Contact your church office or submit a Fluent Form connected to ChurchCore to request updates.', 'churchcore' ); ?></p>
    </section>
</div>
