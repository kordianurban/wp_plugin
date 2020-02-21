<?php

/**
 * Class ToDoList
 * contains all plugin features and inits
 */
Class ToDoList {

    /**
     * Custom post type name
     * used for registration and WP_Query
     */
    const POST_TYPE = KU_TODOLIST_TEXTDOMAIN . '_post';

    /**
     * Name of the post meta
     * used to set the status of ToDo item
     */
    const META_STATUS = KU_TODOLIST_TEXTDOMAIN . '_status';

    /**
     * Nonce
     * for some security
     */
    const NONCE = KU_TODOLIST_TEXTDOMAIN . '_nonce';

    /**
     * ToDoList constructor
     * Contains WP hooks
     */
    public function __construct() {

        add_action( 'init', array($this, 'init') );

        add_action( 'wp_enqueue_scripts', array($this, 'registerAssets') );

        add_action('wp_ajax_nopriv_todolist_handle', array($this, 'ajax'));
        add_action('wp_ajax_todolist_handle', array($this, 'ajax'));

        add_shortcode( 'todolist', array($this, 'widget') );
    }

    /**
     * Initializes plugin features
     */
    public function init() {
        /**
         * There are two different solutions for database storage:
         *
         * 1. I Decided to use as many default wordpress functions as possible,
         * so i am using custom post type and post meta.
         *
         * 2. For this simple plugin it might be better to create custom database table.
         * First step of the process is to call register_activation_hook and create a new table using dbDelta().
         * Then it is possible to store all ToDos using $wpdb.
         */
        $this->registerCustomPostType();
    }

    /**
     * Adds assets
     * including css and js
     */
    public function registerAssets() {
        wp_enqueue_style( KU_TODOLIST_TEXTDOMAIN . '-style', plugin_dir_url( KU_TODOLIST_INDEX ) . 'assets/css/style.css', array(), 'v1' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( KU_TODOLIST_TEXTDOMAIN . '-scripts', plugin_dir_url( KU_TODOLIST_INDEX ) . 'assets/js/scripts.js', array(), 'v1', true );

        wp_localize_script( KU_TODOLIST_TEXTDOMAIN . '-scripts', 'kutodolist_ajax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'ajaxnonce' => wp_create_nonce(self::NONCE),
        ));
    }

    /**
     * Creates custom post type
     * to store specific ToDo items
     */
    private function registerCustomPostType() {
        register_post_type( self::POST_TYPE, array(
            'labels' => array(
                'name' => __( 'ToDo List', KU_TODOLIST_TEXTDOMAIN ),
                'singular_name' => __( 'Task', KU_TODOLIST_TEXTDOMAIN ),
            ),
            'public' => true,
            'supports' => array('title'),
        ));
    }

    /**
     * Handles Ajax Request
     */
    public function ajax() {
        check_ajax_referer( self::NONCE, 'nonce' );

        $data = $_POST; //FIXME esc_attr
        $id = false;
        $post = false;
        $response = array();

        if ( isset($data['id']) ) {
            $id = (int) $data['id'];
        }

        if ( $id ) {
            $post = get_post($id);

            if ( $post && $post->post_type != self::POST_TYPE ) {
                exit;
            }
        }

        switch ( $data['operation'] ) {
            case 'save':
                if ( strlen($data['value']) != 0 ) {
                    $post_data = array(
                        'post_type' => self::POST_TYPE,
                        'post_status' => 'publish',
                        'post_title' => $data['value'],
                    );

                    if ( !$post ) {
                        $response['id'] = wp_insert_post($post_data);
                    }
                    else {
                        $post_data['ID'] = $post->ID;
                        $response['id'] = wp_update_post($post_data);
                    }
                }
                else {
                    $response = array(
                        'id' => 'empty',
                        'value' => $post->post_title,
                    );
                }

                break;

            case 'delete':
                if ( $post ) {
                    wp_delete_post($post->ID);
                }

                break;

            case 'check':
                if ( $post ) {
                    update_post_meta( $post->ID, self::META_STATUS, 'complete' );
                }

                break;

            case 'uncheck':
                if ( $post ) {
                    delete_post_meta( $post->ID, self::META_STATUS );
                }

                break;
        }

        echo json_encode($response);
        exit;
    }


    /**
     * Displays ToDo list widget
     * Shortcode callback
     *
     * @return false|string html output
     */
    public function widget() {
        ob_start();

        $list = new WP_Query(array(
            'post_type' => self::POST_TYPE,
            'posts_per_page' => -1,
            'order' => 'ASC',
        ));

        set_query_var( 'list', $list );
        load_template( KU_TODOLIST_PATH . 'views/widget.php' );

        $output = ob_get_clean();

        return $output;
    }

}
