<?php

/**
 * Plugin Name:       WeTravel Integration
 * Plugin URI:        http://collage.ie
 * Description:       WeTravel Integration plugin.
 * Version:           1.0.0
 * Author:            Rafo
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wetravel-integration
 * Domain Path:       /languages
 */

require_once(plugin_dir_path(__FILE__) . '/vendor/autoload.php');

define('PLUGIN_VERSION', '1.0.0');

use WetravelIntegration\Admin\AdminPanel;

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}


function wetravel_load_styles()
{
    if (isset($_GET['page']) && $_GET['page'] == 'wetravel-integration') {
        wp_enqueue_style('bootstrap', plugin_dir_url(__FILE__) . 'assets/css/bootstrap.min.css', '', '5.3');
        wp_enqueue_style('wetravel-styles', plugin_dir_url(__FILE__) . 'assets/css/styles.css', '', PLUGIN_VERSION);
        wp_enqueue_script('bootstrap-js', plugin_dir_url(__FILE__) . 'assets/js/bootstrap.bundle.min.js', '', '5.3');
    }
}

add_action('admin_enqueue_scripts', 'wetravel_load_styles');
function wetravel_set_default_options()
{
    if (get_option('wetravel_api_key') === false) {
        add_option('wetravel_api_key', 'eyJhbGciOiJSUzI1NiJ9.eyJpZCI6MjI2OTQzLCJzY29wZXMiOlsid3JpdGU6YWxsIiwicmVhZDphbGwiLCJvcmdhbml6ZXIiXSwidmVyIjo0LCJwdWIiOnRydWUsImV4cCI6MjAwNjY0MDAwMCwianRpIjoiYmE3ZDVhOTEtOWE5Mi00N2Q1LWEzYjctYzI2YTk0OWUzOWE2Iiwia2luZCI6InJlZnJlc2gifQ.zeSpTYp6_ko5WInmTLuRCDPD2Yrwhr_hM-1imWiteB_p9nwa5Hfe3pXZMTxjYsHDIKZpKxDuHkZL8VkswXdnOnzOvSfQezDGYd7HNwDl6cw1K1bYY60JJ6R0j-doYxtafYN4ybvGMFO_4NLkdrtPobbxoCDevWXTfunIuv6xMqiHcjzNa6BoM0cVnL-TwVdOPfBSvOBgpEJdOs6C0NE3Rc-_F8DeWrpqjDZUzus2MDzH9Uq0MNN72ZAkce5t4V_jtm0bOYMOnpWIv7AVoEWmMagdM0QKb9ULhxYpU4XzS1uesO4cQVYA4dg-5Z3Cqse3NhDGnjmomiLYsQJzku5nTw', '', 'no');
    }
}

register_activation_hook(__FILE__, 'wetravel_set_default_options');

function wetravel_integration_admin_menu()
{
    $admin_panel = new AdminPanel();
    $admin_panel->create_admin_menu();
}

add_action('admin_menu', 'wetravel_integration_admin_menu');
add_action('admin_init', 'wetravel_integration_admin_init');
add_action('admin_head', 'wetravel_get_tours_id');
add_action('wp_head', 'wetravel_integration_load_tour_data');
add_shortcode('wetravel-dates', 'wetravel_show_tour_dates');

function wetravel_integration_admin_init()
{
    add_action('admin_post_save_wetravel_api_key', 'wetravel_save_options');
}

function wetravel_save_options()
{
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    check_admin_referer('wetravel_integration_nonce');

    $key = get_option('wetravel_api_key');

    if (isset($_POST['api_key'])) {
        $new_key = sanitize_text_field($_POST['api_key']);

        update_option('wetravel_api_key', $new_key);
    }
    wp_redirect(add_query_arg(array('page' => 'wetravel-integration'), admin_url('admin.php')));
    exit;
}

function wetravel_get_tours_id() {
    $tours_ids = get_posts(array(
        'post_type' => 'tours',
        'post_status' => 'publish',
        'fields' => 'ids',
        'orderby' => 'ID',
        'order' => 'ASC',
        'posts_per_page' => -1));
    if(get_option('wetravel_tours_id') === false) {
        add_option('wetravel_tours_id', $tours_ids);
    } else {
        update_option('wetravel_tours_id', $tours_ids);
    }
}

function wetravel_integration_load_tour_data() {
    $post_id = get_queried_object_id();
    $tours = get_option('wetravel_tours_id');

    if(in_array($post_id, $tours)) {
        $wetravel_id = get_field('button_code', $post_id);
        $today = date("Y-m-d");
        $fullYear=date('Y-m-d', strtotime('+1 year'));

        if (get_transient('tour_dates_'.$wetravel_id) === false) {
            $booking_info = wp_remote_get('https://www.wetravel.com/v1/bookings/trips/'.$wetravel_id.'/availability?from_date='.$today.'&to_date='.$fullYear)['body'];
            $response = json_decode($booking_info, true);
            set_transient('tour_dates_'.$wetravel_id, $response, DAY_IN_SECONDS);
            $dates=$response['dates'];
        } else {
            $data = get_transient('tour_dates_'.$wetravel_id);
            $dates = $data['dates'];
        }

        foreach ($dates as $date) {
            ray($date);
            echo('Date: '.$date['date'].'<br>');
            foreach ($date['options'] as $option) {
                echo('Seats left: '.$option['available_amount'].'<br>');
            }
            if(!$date['sold_out']) {
                echo('Not sold out<br>');
            }
        }
    }
}
