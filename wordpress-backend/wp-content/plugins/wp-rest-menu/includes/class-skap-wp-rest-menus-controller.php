<?php

namespace Skapator;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

use WP_REST_Controller;

/**
 * Class WP_REST_Menus_Controller
 *
 *
 * @package Skapator
 */
class WP_REST_Menus_Controller extends WP_REST_Controller {

    /**
     * @var string $namespace
     */
    protected $namespace = 'menus/v1';

    /**
     * @var WP_REST_Menus_Controller
     */
    protected static $instance;

    /**
     * Get instance of class
     *
     * @return WP_REST_Menus_Controller
     */
    public static function instance() {
        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof WP_REST_Menus_Controller ) ) {
            self::$instance = new WP_REST_Menus_Controller;
        }
        return self::$instance;
    }

    /**
     * Register the routes. 
     * 
     */
    public function register_routes() {
        // Get all menus
        register_rest_route( $this->namespace, '/menus', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_menus' )
        ) );

        // Get all menu locations
        register_rest_route( $this->namespace, '/menus/locations', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_menu_locations' )
        ) );

        // Get menu by term_id
        register_rest_route( $this->namespace, '/menus/(?P<id>[0-9(-]+)', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_menu_items' ),
            'args' => array(
                'id' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_numeric( $param );
                    }
                ),
                'fields' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                ),
                'nested' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return absint( $param );
                    }
                ),
            ),
        ) );

        // Get menu by location slug
        register_rest_route( $this->namespace, '/menus/locations/(?P<slug>[a-zA-Z(-]+)', array(
            'methods'  => \WP_REST_Server::READABLE,
            'callback' => array( $this, 'get_location_menu_items' ),
            'args' => array(
                'slug' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                ),
                'fields' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                ),
                'nested' => array(
                    'validate_callback' => function($param, $request, $key) {
                        return absint( $param );
                    }
                ),
            ),
        ) );
    }

    /**
     * Get all menus
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_menus() {
        $menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) );
        
        return rest_ensure_response( $menus );
    }

    /**
     * Get menu locations
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_menu_locations() {
        $menus = [];

        foreach ( get_registered_nav_menus() as $slug => $description ) {
            $obj = new \stdClass;
            $obj->slug = $slug;
            $obj->description = $description;
            $menus[] = $obj;
        }

        return rest_ensure_response( $menus );
    }

    /**
     * Get menu items
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_menu_items( \WP_REST_Request $request ) {
        $menu = null;

        // If WPML is active we can get the translated id for the current language
        // by passing any lang id
        $id = apply_filters( 'wpml_object_id', (int) $request->get_param( 'id' ), 'nav_menu', true );

        if ( $menu_items = wp_get_nav_menu_items( $id ) ) {
            $menu = $this->get_item_fields( $request, $menu_items );
        }

        return rest_ensure_response( $menu );
    }

    /**
     * Get menu location items
     *
     * @return WP_Error|WP_HTTP_Response|WP_REST_Response|mixed
     */
    public function get_location_menu_items( \WP_REST_Request $request ) {;
        $menu = null;

        if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $request->get_param( 'slug' ) ] ) ) {
            $menu = get_term( $locations[ $request->get_param( 'slug' ) ] );
            if ( $menu_items = wp_get_nav_menu_items( $menu->term_id ) ) {
                $menu = $this->get_item_fields( $request, $menu_items );
            }
        }

        return rest_ensure_response( $menu );
    }

    /**
     * Get menu item response fields
     *
     * @return array
     */
    private function get_item_fields( \WP_REST_Request $request, $menu_items  ) {
        $menu   = [];
        $nested = $this->nested_request( $request );

        $menu_items = apply_filters( 'skap_wp_rest_menu_items', $menu_items );

        foreach( $menu_items as $item ) {
            $item->meta = $this->get_item_meta( $item->ID );

            // Add a filter so we can filter fields programatically
            $fields = apply_filters( 'skap_wp_rest_menu_item_fields', $this->filter_fields( $request ) );

            if( $fields ) {
                $filtered = new \stdClass;
                foreach( $item as $key => $val ) {
                    if( in_array( $key, $fields ) ) {
                        $filtered->$key = $item->$key;
                    }

                    $filtered->ID = $item->ID;
                    $filtered->menu_item_parent = $item->menu_item_parent;
                }
            } else {
                $filtered = $item;
            }

            $menu[] = $filtered;
        }

        if( $nested ) {
            $menu = $this->nest_items( $menu );
        }

        return $menu;
    }

    /**
     * Nest children items in parent
     *
     * @return array
     */
    private function nest_items( $menu_items ) {
        $parents = array_values( 
            array_filter( $menu_items, function($m) {
                return $m->menu_item_parent == 0;
            }) 
        );

        foreach( $parents as $parent ) {
            $parent->children = null;

            $children = array_filter( $menu_items, function($m) use ($parent) {
                return $m->menu_item_parent == $parent->ID;
            });

            if( $children ) {
                $parent->children = array_values( $children );
            }
        }

        return $parents;
    }

    private function nested_request( \WP_REST_Request $request ) {
        return ( $request->get_param( 'nested' ) && $request->get_param( 'nested' ) == 1 ) || false;
    }

    /**
     * Get item custom fields except built in
     *
     * TODO: Add meta field filter
     * @return array
     */
    private function get_item_meta( $item_id ) {
        $meta = [];

        $post_meta = get_post_custom( $item_id );

        // remove uneccessary fields
        if( $post_meta ) {
            foreach( $post_meta as $key => $val ) {
                if( $key[0] !== '_' ) {
                    $meta[$key] = $val[0] ?: null;
                }
            }
        }

        return $meta ?: null;
    }

    /**
     * Get filters if set
     *
     * @return mixed array|boolean
     */
    private function filter_fields( $request ) {
        $fields = (string) $request->get_param( 'fields' );

        if ( $fields = (string) $request->get_param( 'fields' ) ) {
            $array = explode( ',', $fields );
            return ! empty( $array ) ? $array : false;
        }

        return false;
    }

    /**
     * Visual log, print_r wrapper
     *
     */
    public function log( $data ) {
        echo '<pre style="background:#eee;padding:5px;margin-bottom:15px">';
        print_r( $data );
        echo '</pre>';
    }
}