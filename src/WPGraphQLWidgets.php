<?php

use WPGraphQL\AppContext;
use WPGraphQLWidgets\Registry;
use WPGraphQLWidgets\Data\Loader\WidgetLoader;
use WPGraphQLWidgets\Data\Loader\SidebarLoader;

class WPGraphQLWidgets
{
    private static $_instance;

    public static function run()
    {
        if (!isset(self::$_instance) || !self::$_instance instanceof WPGraphQLWidgets) {
            self::$_instance = new WPGraphQLWidgets();
            self::$_instance->init();
        }

        return self::$_instance;
    }

    private function init()
    {
        self::loadDependecies();

        add_action('wp_loaded', [$this, 'initRegistry']);
        add_action('graphql_register_types', [__CLASS__, 'registerFields']);
        add_filter('graphql_data_loaders', [__CLASS__, 'registerLoader'], 10, 2);
    }

    private static function loadDependecies()
    {
        // phpcs:ignore
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    public function initRegistry()
    {
        Registry::init();
    }

    public static function registerLoader( $loaders, AppContext $context )
    {
        $loaders['widget'] = new WidgetLoader($context);
        $loaders['sidebar'] = new SidebarLoader($context);
        return $loaders;
    }

    public static function registerFields()
    {
        register_graphql_field(
            'RootQuery',
            'widget',
            [
                'type' => 'WidgetInterface',
                'description' => 'Returns a widget',
                'args'        => [
                    'id' => [
                        'type' => [
                            'non_null' => 'ID'
                        ]
                    ]
                ],
                'resolve' => function ( $root, $args, $context, $info ) {
                    return $context->get_loader('widget')->load_deferred($args['id']);
                }
            ] 
        );

        register_graphql_field(
            'RootQuery',
            'sidebar',
            [
                'type' => 'Sidebar',
                'description' => 'Returns a sidebar',
                'args'        => [
                    'sidebar' => [
                        'type' => [
                            'non_null' => 'SidebarEnum'
                        ]
                    ]
                ],
                'resolve'     => function ( $source, array $args, $context, $info ) {
                    return $context->get_loader('sidebar')->load_deferred($args['sidebar']);
                },
            ] 
        );
    }
}
