<?php
/**
 * Families management screen.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$families = get_posts(
    array(
        'post_type'      => 'cc_family',
        'posts_per_page' => 20,
        'post_status'    => 'any',
    )
);
?>
<div class="wrap churchcore-wrap">
    <h1><?php esc_html_e( 'Families', 'churchcore' ); ?></h1>
    <?php settings_errors( 'churchcore' ); ?>
    <div class="churchcore-grid">
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Add Family', 'churchcore' ); ?></h2>
            <form method="post">
                <?php wp_nonce_field( 'churchcore_action' ); ?>
                <input type="hidden" name="churchcore_action" value="add_family" />
                <div class="churchcore-form-group">
                    <label for="family_name"><?php esc_html_e( 'Family Name', 'churchcore' ); ?></label>
                    <input type="text" id="family_name" name="family_name" class="regular-text" required />
                </div>
                <div class="churchcore-form-group">
                    <label for="family_status"><?php esc_html_e( 'Status', 'churchcore' ); ?></label>
                    <select id="family_status" name="family_status">
                        <option value="publish"><?php esc_html_e( 'Active', 'churchcore' ); ?></option>
                        <option value="draft"><?php esc_html_e( 'Prospect', 'churchcore' ); ?></option>
                        <option value="follow-up"><?php esc_html_e( 'Follow Up Needed', 'churchcore' ); ?></option>
                    </select>
                </div>
                <?php submit_button( __( 'Add Family', 'churchcore' ) ); ?>
            </form>
        </div>
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Family Directory', 'churchcore' ); ?></h2>
            <table class="churchcore-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Family', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Members', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'churchcore' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $families as $family ) :
                    $member_count = count( get_posts(
                        array(
                            'post_type'      => 'cc_person',
                            'posts_per_page' => -1,
                            'post_status'    => 'any',
                            'meta_query'     => array(
                                array(
                                    'key'   => '_churchcore_family_id',
                                    'value' => $family->ID,
                                ),
                            ),
                        )
                    ) );
                    $status_obj   = get_post_status_object( $family->post_status );
                    $status_label = $status_obj ? $status_obj->label : $family->post_status;
                    ?>
                    <tr>
                        <td><a href="<?php echo esc_url( get_edit_post_link( $family->ID ) ); ?>"><?php echo esc_html( get_the_title( $family ) ); ?></a></td>
                        <td><?php echo esc_html( $member_count ); ?></td>
                        <td><?php echo esc_html( $status_label ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
