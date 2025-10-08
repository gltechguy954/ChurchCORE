<?php
/**
 * Admin menu and UI rendering.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Handles admin screens for ChurchCore.
 */
class ChurchCore_Admin_Menu {
    /**
     * Registers admin menus.
     */
    public function register_menus() {
        add_menu_page(
            __( 'ChurchCore', 'churchcore' ),
            __( 'ChurchCore', 'churchcore' ),
            'manage_churchcore',
            'churchcore',
            array( $this, 'render_dashboard' ),
            'dashicons-church',
            58
        );

        add_submenu_page(
            'churchcore',
            __( 'Families', 'churchcore' ),
            __( 'Families', 'churchcore' ),
            'manage_churchcore',
            'churchcore-families',
            array( $this, 'render_families' )
        );

        add_submenu_page(
            'churchcore',
            __( 'People', 'churchcore' ),
            __( 'People', 'churchcore' ),
            'manage_churchcore',
            'churchcore-people',
            array( $this, 'render_people' )
        );

        add_submenu_page(
            'churchcore',
            __( 'Notes', 'churchcore' ),
            __( 'Notes', 'churchcore' ),
            'manage_churchcore',
            'churchcore-notes',
            array( $this, 'render_notes' )
        );

        add_submenu_page(
            'churchcore',
            __( 'Integrations', 'churchcore' ),
            __( 'Integrations', 'churchcore' ),
            'manage_churchcore',
            'churchcore-integrations',
            array( $this, 'render_integrations' )
        );
    }

    /**
     * Load admin assets.
     */
    public function enqueue_assets( $hook_suffix ) {
        if ( false === strpos( $hook_suffix, 'churchcore' ) ) {
            return;
        }

        wp_enqueue_style(
            'churchcore-admin',
            CHURCHCORE_PLUGIN_URL . 'includes/admin/churchcore-admin.css',
            array(),
            CHURCHCORE_PLUGIN_VERSION
        );
    }

    /**
     * Handles admin POST actions.
     */
    public function handle_actions() {
        if ( empty( $_POST['churchcore_action'] ) ) {
            return;
        }

        if ( ! current_user_can( 'manage_churchcore' ) ) {
            wp_die( esc_html__( 'You do not have permission to modify ChurchCore records.', 'churchcore' ) );
        }

        if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'churchcore_action' ) ) {
            wp_die( esc_html__( 'Security check failed.', 'churchcore' ) );
        }

        $action  = sanitize_key( wp_unslash( $_POST['churchcore_action'] ) );
        $handled = false;

        switch ( $action ) {
            case 'add_family':
                $this->create_family();
                $handled = true;
                break;
            case 'add_person':
                $this->create_person();
                $handled = true;
                break;
            case 'add_note':
                $this->create_note();
                $handled = true;
                break;
        }

        if ( $handled ) {
            $redirect = wp_get_referer() ? wp_get_referer() : admin_url( 'admin.php?page=churchcore' );
            wp_safe_redirect( $redirect );
            exit;
        }
    }

    /**
     * Dashboard screen.
     */
    public function render_dashboard() {
        include CHURCHCORE_PLUGIN_DIR . 'includes/admin/views/dashboard.php';
    }

    /**
     * Families screen.
     */
    public function render_families() {
        include CHURCHCORE_PLUGIN_DIR . 'includes/admin/views/families.php';
    }

    /**
     * People screen.
     */
    public function render_people() {
        include CHURCHCORE_PLUGIN_DIR . 'includes/admin/views/people.php';
    }

    /**
     * Notes screen.
     */
    public function render_notes() {
        include CHURCHCORE_PLUGIN_DIR . 'includes/admin/views/notes.php';
    }

    /**
     * Integrations screen.
     */
    public function render_integrations() {
        include CHURCHCORE_PLUGIN_DIR . 'includes/admin/views/integrations.php';
    }

    /**
     * Adds a family record.
     */
    protected function create_family() {
        $family_name = isset( $_POST['family_name'] ) ? sanitize_text_field( wp_unslash( $_POST['family_name'] ) ) : '';
        $status      = isset( $_POST['family_status'] ) ? sanitize_text_field( wp_unslash( $_POST['family_status'] ) ) : 'active';

        if ( empty( $family_name ) ) {
            add_settings_error( 'churchcore', 'family_name_required', __( 'Family name is required.', 'churchcore' ) );
            return;
        }

        $post_id = wp_insert_post(
            array(
                'post_type'   => 'cc_family',
                'post_title'  => $family_name,
                'post_status' => $status,
            ),
            true
        );

        if ( is_wp_error( $post_id ) ) {
            add_settings_error( 'churchcore', 'family_create_failed', $post_id->get_error_message() );
        } else {
            add_settings_error( 'churchcore', 'family_create_success', __( 'Family created successfully.', 'churchcore' ), 'updated' );
        }
    }

    /**
     * Adds a person record.
     */
    protected function create_person() {
        $display_name = isset( $_POST['person_name'] ) ? sanitize_text_field( wp_unslash( $_POST['person_name'] ) ) : '';
        $email        = isset( $_POST['person_email'] ) ? sanitize_email( wp_unslash( $_POST['person_email'] ) ) : '';
        $phone        = isset( $_POST['person_phone'] ) ? sanitize_text_field( wp_unslash( $_POST['person_phone'] ) ) : '';
        $role         = isset( $_POST['person_role'] ) ? sanitize_text_field( wp_unslash( $_POST['person_role'] ) ) : '';
        $family_id    = isset( $_POST['person_family'] ) ? absint( $_POST['person_family'] ) : 0;

        if ( empty( $display_name ) ) {
            add_settings_error( 'churchcore', 'person_name_required', __( 'Person name is required.', 'churchcore' ) );
            return;
        }

        $post_id = wp_insert_post(
            array(
                'post_type'   => 'cc_person',
                'post_title'  => $display_name,
                'post_status' => 'publish',
            ),
            true
        );

        if ( is_wp_error( $post_id ) ) {
            add_settings_error( 'churchcore', 'person_create_failed', $post_id->get_error_message() );
            return;
        }

        update_post_meta( $post_id, '_churchcore_email', $email );
        update_post_meta( $post_id, '_churchcore_phone', $phone );
        update_post_meta( $post_id, '_churchcore_family_id', $family_id );

        if ( ! empty( $role ) ) {
            wp_set_post_terms( $post_id, array( $role ), 'cc_role', false );
        }

        add_settings_error( 'churchcore', 'person_create_success', __( 'Person created successfully.', 'churchcore' ), 'updated' );
    }

    /**
     * Adds a note record.
     */
    protected function create_note() {
        global $wpdb;

        $related_type = isset( $_POST['note_related_type'] ) ? sanitize_text_field( wp_unslash( $_POST['note_related_type'] ) ) : '';
        $related_id   = isset( $_POST['note_related_id'] ) ? absint( $_POST['note_related_id'] ) : 0;
        $content      = isset( $_POST['note_content'] ) ? wp_kses_post( wp_unslash( $_POST['note_content'] ) ) : '';
        $visibility   = isset( $_POST['note_visibility'] ) ? sanitize_text_field( wp_unslash( $_POST['note_visibility'] ) ) : 'private';

        $allowed_types = array(
            'person' => 'cc_person',
            'family' => 'cc_family',
        );

        if ( empty( $related_type ) || empty( $related_id ) || empty( $content ) ) {
            add_settings_error( 'churchcore', 'note_required', __( 'All note fields are required.', 'churchcore' ) );
            return;
        }

        if ( ! isset( $allowed_types[ $related_type ] ) ) {
            add_settings_error( 'churchcore', 'note_type_invalid', __( 'Invalid note relationship selected.', 'churchcore' ) );
            return;
        }

        $related_post_type = get_post_type( $related_id );
        if ( $allowed_types[ $related_type ] !== $related_post_type ) {
            add_settings_error( 'churchcore', 'note_post_type_mismatch', __( 'The selected record does not match the relationship type.', 'churchcore' ) );
            return;
        }

        $table = $wpdb->prefix . 'churchcore_notes';

        $inserted = $wpdb->insert(
            $table,
            array(
                'related_type' => $related_type,
                'related_id'   => $related_id,
                'author_id'    => get_current_user_id(),
                'content'      => $content,
                'visibility'   => $visibility,
                'created_at'   => current_time( 'mysql', 1 ),
            ),
            array( '%s', '%d', '%d', '%s', '%s', '%s' )
        );

        if ( false === $inserted ) {
            add_settings_error( 'churchcore', 'note_create_failed', __( 'Failed to create note.', 'churchcore' ) );
        } else {
            add_settings_error( 'churchcore', 'note_create_success', __( 'Note created successfully.', 'churchcore' ), 'updated' );
        }
    }
}
