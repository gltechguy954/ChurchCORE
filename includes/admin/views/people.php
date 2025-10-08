<?php
/**
 * People management screen.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$people   = get_posts(
    array(
        'post_type'      => 'cc_person',
        'posts_per_page' => 20,
        'post_status'    => 'any',
    )
);
$families = get_posts(
    array(
        'post_type'      => 'cc_family',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'orderby'        => 'title',
        'order'          => 'ASC',
    )
);
$roles = get_terms(
    array(
        'taxonomy'   => 'cc_role',
        'hide_empty' => false,
    )
);
?>
<div class="wrap churchcore-wrap">
    <h1><?php esc_html_e( 'People', 'churchcore' ); ?></h1>
    <?php settings_errors( 'churchcore' ); ?>
    <div class="churchcore-grid">
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Add Person', 'churchcore' ); ?></h2>
            <form method="post">
                <?php wp_nonce_field( 'churchcore_action' ); ?>
                <input type="hidden" name="churchcore_action" value="add_person" />
                <div class="churchcore-form-group">
                    <label for="person_name"><?php esc_html_e( 'Name', 'churchcore' ); ?></label>
                    <input type="text" id="person_name" name="person_name" class="regular-text" required />
                </div>
                <div class="churchcore-form-group">
                    <label for="person_email"><?php esc_html_e( 'Email', 'churchcore' ); ?></label>
                    <input type="email" id="person_email" name="person_email" class="regular-text" />
                </div>
                <div class="churchcore-form-group">
                    <label for="person_phone"><?php esc_html_e( 'Phone', 'churchcore' ); ?></label>
                    <input type="text" id="person_phone" name="person_phone" class="regular-text" />
                </div>
                <div class="churchcore-form-group">
                    <label for="person_family"><?php esc_html_e( 'Family', 'churchcore' ); ?></label>
                    <select id="person_family" name="person_family">
                        <option value="0"><?php esc_html_e( 'Unassigned', 'churchcore' ); ?></option>
                        <?php foreach ( $families as $family ) : ?>
                            <option value="<?php echo esc_attr( $family->ID ); ?>"><?php echo esc_html( get_the_title( $family ) ); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="churchcore-form-group">
                    <label for="person_role"><?php esc_html_e( 'Role', 'churchcore' ); ?></label>
                    <input type="text" id="person_role" name="person_role" class="regular-text" list="churchcore_roles" />
                    <datalist id="churchcore_roles">
                        <?php foreach ( $roles as $role ) : ?>
                            <option value="<?php echo esc_attr( $role->name ); ?>"></option>
                        <?php endforeach; ?>
                    </datalist>
                </div>
                <?php submit_button( __( 'Add Person', 'churchcore' ) ); ?>
            </form>
        </div>
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'People Directory', 'churchcore' ); ?></h2>
            <table class="churchcore-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Name', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Family', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Email', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Role', 'churchcore' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $people as $person ) :
                    $family_id = get_post_meta( $person->ID, '_churchcore_family_id', true );
                    $family    = $family_id ? get_post( $family_id ) : null;
                    $terms     = wp_get_post_terms( $person->ID, 'cc_role', array( 'fields' => 'names' ) );
                    ?>
                    <tr>
                        <td><a href="<?php echo esc_url( get_edit_post_link( $person->ID ) ); ?>"><?php echo esc_html( get_the_title( $person ) ); ?></a></td>
                        <td><?php echo esc_html( $family ? get_the_title( $family ) : __( 'Unassigned', 'churchcore' ) ); ?></td>
                        <td><?php echo esc_html( get_post_meta( $person->ID, '_churchcore_email', true ) ); ?></td>
                        <td><?php echo esc_html( implode( ', ', $terms ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
