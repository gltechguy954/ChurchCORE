<?php
/**
 * Registers custom post types and taxonomies.
 *
 * @package ChurchCore
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Custom post type registrar.
 */
class ChurchCore_Custom_Post_Types {
    /**
     * Register post types and taxonomies.
     */
    public function register() {
        $this->register_family_post_type();
        $this->register_person_post_type();
        $this->register_relationship_taxonomy();
    }

    /**
     * Families.
     */
    protected function register_family_post_type() {
        $labels = array(
            'name'               => __( 'Families', 'churchcore' ),
            'singular_name'      => __( 'Family', 'churchcore' ),
            'add_new_item'       => __( 'Add New Family', 'churchcore' ),
            'edit_item'          => __( 'Edit Family', 'churchcore' ),
            'new_item'           => __( 'New Family', 'churchcore' ),
            'view_item'          => __( 'View Family', 'churchcore' ),
            'search_items'       => __( 'Search Families', 'churchcore' ),
            'not_found'          => __( 'No families found.', 'churchcore' ),
            'not_found_in_trash' => __( 'No families found in Trash.', 'churchcore' ),
        );

        $args = array(
            'label'               => __( 'Families', 'churchcore' ),
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'supports'            => array( 'title', 'editor', 'author' ),
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'has_archive'         => false,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-groups',
        );

        register_post_type( 'cc_family', $args );
    }

    /**
     * People.
     */
    protected function register_person_post_type() {
        $labels = array(
            'name'               => __( 'People', 'churchcore' ),
            'singular_name'      => __( 'Person', 'churchcore' ),
            'add_new_item'       => __( 'Add New Person', 'churchcore' ),
            'edit_item'          => __( 'Edit Person', 'churchcore' ),
            'new_item'           => __( 'New Person', 'churchcore' ),
            'view_item'          => __( 'View Person', 'churchcore' ),
            'search_items'       => __( 'Search People', 'churchcore' ),
            'not_found'          => __( 'No people found.', 'churchcore' ),
            'not_found_in_trash' => __( 'No people found in Trash.', 'churchcore' ),
        );

        $args = array(
            'label'               => __( 'People', 'churchcore' ),
            'labels'              => $labels,
            'public'              => false,
            'show_ui'             => true,
            'show_in_menu'        => false,
            'supports'            => array( 'title', 'editor', 'author', 'custom-fields' ),
            'capability_type'     => 'post',
            'map_meta_cap'        => true,
            'has_archive'         => false,
            'show_in_rest'        => true,
            'menu_icon'           => 'dashicons-id',
        );

        register_post_type( 'cc_person', $args );
    }

    /**
     * Household and ministry relationships.
     */
    protected function register_relationship_taxonomy() {
        $labels = array(
            'name'              => __( 'Ministry Roles', 'churchcore' ),
            'singular_name'     => __( 'Ministry Role', 'churchcore' ),
            'search_items'      => __( 'Search Roles', 'churchcore' ),
            'all_items'         => __( 'All Roles', 'churchcore' ),
            'edit_item'         => __( 'Edit Role', 'churchcore' ),
            'update_item'       => __( 'Update Role', 'churchcore' ),
            'add_new_item'      => __( 'Add New Role', 'churchcore' ),
            'new_item_name'     => __( 'New Role Name', 'churchcore' ),
            'menu_name'         => __( 'Roles', 'churchcore' ),
        );

        $args = array(
            'hierarchical'      => false,
            'labels'            => $labels,
            'show_ui'           => true,
            'show_in_rest'      => true,
            'show_admin_column' => true,
            'rewrite'           => false,
        );

        register_taxonomy( 'cc_role', array( 'cc_person' ), $args );
    }
}
