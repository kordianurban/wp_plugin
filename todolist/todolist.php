<?php

/**
 * Plugin Name: ToDo List by KU
 * Plugin URI: http://kordianurban.pl
 * Description: This is a simple plugin which allows you to create your own ToDo list
 * Version: 1.0
 * Author: Kordian Urban
 * Author URI: http://kordianurban.pl
 * License: GPL2
 */

if ( ! defined( 'KU_TODOLIST_TEXTDOMAIN' ) ) {
    define( 'KU_TODOLIST_TEXTDOMAIN', 'kutodolist' );
}

if ( ! defined( 'KU_TODOLIST_INDEX' ) ) {
    define( 'KU_TODOLIST_INDEX', __FILE__);
}

if ( ! defined( 'KU_TODOLIST_PATH' ) ) {
    define( 'KU_TODOLIST_PATH', plugin_dir_path(__FILE__) );
}

require_once KU_TODOLIST_PATH . 'src/ToDoList.php';

new ToDoList();