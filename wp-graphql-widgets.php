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

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/src/WPGraphQLWidgets.php';

WPGraphQLWidgets::init();
