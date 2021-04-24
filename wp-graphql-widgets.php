<?php
/**
 * Plugin Name: WPGraphQL Widgets
 * Version: 0.0.1
 * Author: Benedict Adams
 * Text Domain: wp-graphql-widgets
 *
 * @package wp-graphql-widgets
 */

defined('ABSPATH') || exit;

if (! class_exists('WPGraphQLWidgets') ) {
    // phpcs:ignore
	require_once __DIR__ . '/src/WPGraphQLWidgets.php';
}

WPGraphQLWidgets::run();
