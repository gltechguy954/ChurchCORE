<?php
/**
 * Main plugin loader.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once CHURCHCORE_PLUGIN_DIR . 'includes/class-churchcore-custom-post-types.php';
require_once CHURCHCORE_PLUGIN_DIR . 'includes/class-churchcore-admin-menu.php';
require_once CHURCHCORE_PLUGIN_DIR . 'includes/class-churchcore-integrations.php';
require_once CHURCHCORE_PLUGIN_DIR . 'includes/class-churchcore-shortcodes.php';

/**
 * Core plugin class.
 */
class ChurchCore_Plugin {
    /**
     * Custom post type registrar.
     *
     * @var ChurchCore_Custom_Post_Types
     */
    protected $cpt;

    /**
     * Admin menu handler.
     *
     * @var ChurchCore_Admin_Menu
     */
    protected $admin_menu;

    /**
     * Integration handler.
     *
     * @var ChurchCore_Integrations
     */
    protected $integrations;

    /**
     * Shortcode handler.
     *
     * @var ChurchCore_Shortcodes
     */
    protected $shortcodes;

    /**
     * Bootstraps dependencies.
     */
    public function __construct() {
        $this->cpt           = new ChurchCore_Custom_Post_Types();
        $this->admin_menu    = new ChurchCore_Admin_Menu();
        $this->integrations  = new ChurchCore_Integrations();
        $this->shortcodes    = new ChurchCore_Shortcodes();
    }

    /**
     * Registers hooks with WordPress.
     */
    public function run() {
        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this->cpt, 'register' ) );
        add_action( 'init', array( $this, 'register_roles' ) );
        add_action( 'init', array( $this, 'register_statuses' ) );
        add_action( 'admin_menu', array( $this->admin_menu, 'register_menus' ) );
        add_action( 'admin_init', array( $this->admin_menu, 'handle_actions' ) );
        add_action( 'admin_enqueue_scripts', array( $this->admin_menu, 'enqueue_assets' ) );
        add_action( 'admin_notices', array( $this->integrations, 'render_notices' ) );
        add_action( 'rest_api_init', array( $this, 'register_rest_routes' ) );
        $this->shortcodes->register();
    }

    /**
     * Loads the plugin text domain for translation.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'churchcore', false, dirname( plugin_basename( CHURCHCORE_PLUGIN_FILE ) ) . '/languages/' );
    }

    /**
     * Creates data tables on activation.
     */
    public static function activate() {
        global $wpdb;

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();

        $families_table = $wpdb->prefix . 'churchcore_families';
        $people_table   = $wpdb->prefix . 'churchcore_people';
        $notes_table    = $wpdb->prefix . 'churchcore_notes';

        $sql_families = "CREATE TABLE {$families_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            family_name VARCHAR(255) NOT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'active',
            metadata LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) {$charset_collate};";

        $sql_people = "CREATE TABLE {$people_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            family_id BIGINT(20) UNSIGNED NULL,
            display_name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NULL,
            phone VARCHAR(50) NULL,
            role VARCHAR(100) NULL,
            metadata LONGTEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY family_id (family_id)
        ) {$charset_collate};";

        $sql_notes = "CREATE TABLE {$notes_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            related_type VARCHAR(50) NOT NULL,
            related_id BIGINT(20) UNSIGNED NOT NULL,
            author_id BIGINT(20) UNSIGNED NOT NULL,
            content LONGTEXT NOT NULL,
            visibility VARCHAR(20) NOT NULL DEFAULT 'private',
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY related (related_type, related_id)
        ) {$charset_collate};";

        dbDelta( $sql_families );
        dbDelta( $sql_people );
        dbDelta( $sql_notes );

        $instance = new self();
        $instance->register_roles();

        flush_rewrite_rules();
    }

    /**
     * Removes rewrites on deactivation.
     */
    public static function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Registers custom roles used by the CMS.
     */
    public function register_roles() {
        if ( ! get_role( 'churchcore_manager' ) ) {
            add_role(
                'churchcore_manager',
                __( 'Church Manager', 'churchcore' ),
                array(
                    'read'              => true,
                    'edit_posts'        => true,
                    'publish_posts'     => true,
                    'list_users'        => true,
                    'create_users'      => true,
                    'delete_users'      => false,
                    'manage_churchcore' => true,
                )
            );
        }

        if ( ! get_role( 'churchcore_volunteer' ) ) {
            add_role(
                'churchcore_volunteer',
                __( 'Church Volunteer', 'churchcore' ),
                array(
                    'read'              => true,
                    'edit_posts'        => false,
                    'churchcore_portal' => true,
                )
            );
        }

        $admin_role = get_role( 'administrator' );
        if ( $admin_role && ! $admin_role->has_cap( 'manage_churchcore' ) ) {
            $admin_role->add_cap( 'manage_churchcore' );
        }
    }

    /**
     * Registers custom post statuses for pastoral care.
     */
    public function register_statuses() {
        register_post_status(
            'pastoral-care',
            array(
                'label'                     => _x( 'Pastoral Care', 'post', 'churchcore' ),
                'public'                    => true,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
            )
        );

        register_post_status(
            'follow-up',
            array(
                'label'                     => _x( 'Follow Up Needed', 'post', 'churchcore' ),
                'public'                    => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
            )
        );
    }

    /**
     * Registers REST API routes for the portal.
     */
    public function register_rest_routes() {
        register_rest_route(
            'churchcore/v1',
            '/summary',
            array(
                'methods'             => 'GET',
                'permission_callback' => array( $this, 'can_access_portal' ),
                'callback'            => array( $this, 'get_summary' ),
            )
        );
    }

    /**
     * REST capability check.
     *
     * @return bool
     */
    public function can_access_portal() {
        return current_user_can( 'manage_churchcore' ) || current_user_can( 'churchcore_portal' );
    }

    /**
     * Builds REST response summarizing key data.
     *
     * @param WP_REST_Request $request Request.
     * @return WP_REST_Response
     */
    public function get_summary( $request ) {
        $families = get_posts(
            array(
                'post_type'      => 'cc_family',
                'posts_per_page' => 5,
                'post_status'    => 'any',
            )
        );

        $members = get_posts(
            array(
                'post_type'      => 'cc_person',
                'posts_per_page' => 5,
                'post_status'    => 'any',
            )
        );

        $data = array(
            'families' => array_map( array( $this, 'format_post_for_rest' ), $families ),
            'people'   => array_map( array( $this, 'format_post_for_rest' ), $members ),
        );

        return rest_ensure_response( $data );
    }

    /**
     * Formats posts for REST output.
     *
     * @param WP_Post $post Post object.
     * @return array
     */
    protected function format_post_for_rest( $post ) {
        return array(
            'id'      => $post->ID,
            'title'   => get_the_title( $post ),
            'status'  => $post->post_status,
            'link'    => get_edit_post_link( $post->ID, '' ),
        );
    }
}
