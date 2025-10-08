<?php
/**
 * Notes management screen.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $wpdb;

$table  = $wpdb->prefix . 'churchcore_notes';
$notes  = $wpdb->get_results( "SELECT * FROM {$table} ORDER BY created_at DESC LIMIT 50" );
$people = get_posts(
    array(
        'post_type'      => 'cc_person',
        'posts_per_page' => -1,
        'post_status'    => 'any',
        'orderby'        => 'title',
        'order'          => 'ASC',
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
?>
<div class="wrap churchcore-wrap">
    <h1><?php esc_html_e( 'Notes', 'churchcore' ); ?></h1>
    <?php settings_errors( 'churchcore' ); ?>
    <div class="churchcore-grid">
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Add Note', 'churchcore' ); ?></h2>
            <form method="post">
                <?php wp_nonce_field( 'churchcore_action' ); ?>
                <input type="hidden" name="churchcore_action" value="add_note" />
                <div class="churchcore-form-group">
                    <label for="note_related_type"><?php esc_html_e( 'Related To', 'churchcore' ); ?></label>
                    <select id="note_related_type" name="note_related_type" required>
                        <option value="person"><?php esc_html_e( 'Person', 'churchcore' ); ?></option>
                        <option value="family"><?php esc_html_e( 'Family', 'churchcore' ); ?></option>
                    </select>
                </div>
                <div class="churchcore-form-group">
                    <label for="note_related_id"><?php esc_html_e( 'Record', 'churchcore' ); ?></label>
                    <select id="note_related_id" name="note_related_id" required>
                        <optgroup label="<?php esc_attr_e( 'People', 'churchcore' ); ?>">
                            <?php foreach ( $people as $person ) : ?>
                                <option value="<?php echo esc_attr( $person->ID ); ?>" data-type="person"><?php echo esc_html( get_the_title( $person ) ); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="<?php esc_attr_e( 'Families', 'churchcore' ); ?>">
                            <?php foreach ( $families as $family ) : ?>
                                <option value="<?php echo esc_attr( $family->ID ); ?>" data-type="family"><?php echo esc_html( get_the_title( $family ) ); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
                <div class="churchcore-form-group">
                    <label for="note_content"><?php esc_html_e( 'Note', 'churchcore' ); ?></label>
                    <textarea id="note_content" name="note_content" rows="4" class="large-text" required></textarea>
                </div>
                <div class="churchcore-form-group">
                    <label for="note_visibility"><?php esc_html_e( 'Visibility', 'churchcore' ); ?></label>
                    <select id="note_visibility" name="note_visibility">
                        <option value="private"><?php esc_html_e( 'Private', 'churchcore' ); ?></option>
                        <option value="team"><?php esc_html_e( 'Team', 'churchcore' ); ?></option>
                    </select>
                </div>
                <?php submit_button( __( 'Add Note', 'churchcore' ) ); ?>
            </form>
        </div>
        <div class="churchcore-card">
            <h2><?php esc_html_e( 'Recent Notes', 'churchcore' ); ?></h2>
            <table class="churchcore-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Date', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Related', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Author', 'churchcore' ); ?></th>
                        <th><?php esc_html_e( 'Visibility', 'churchcore' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ( $notes as $note ) :
                    $link = 'person' === $note->related_type ? get_edit_post_link( $note->related_id ) : get_edit_post_link( $note->related_id );
                    ?>
                    <tr>
                        <td><?php echo esc_html( mysql2date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $note->created_at ) ); ?></td>
                        <td><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( get_the_title( $note->related_id ) ); ?></a></td>
                        <td><?php echo esc_html( get_the_author_meta( 'display_name', $note->author_id ) ); ?></td>
                        <td><?php echo esc_html( ucfirst( $note->visibility ) ); ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
