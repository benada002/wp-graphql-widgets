<?php

use WPGraphQL\AppContext;
use WPGraphQLWidgets\Connection\WPMediaImageWidget;
use WPGraphQLWidgets\Registry;
use WPGraphQLWidgets\Data\Connection\WidgetConnectionResolver;
use WPGraphQLWidgets\Data\Loader\WidgetLoader;
use WPGraphQLWidgets\Connection\WPNavMenuWidget;
use WPGraphQLWidgets\Connection\WPWidgetRecentComments;
use WPGraphQLWidgets\Data\Connection\SidebarConnectionResolver;
use WPGraphQLWidgets\Data\Loader\SidebarLoader;
use WPGraphQLWidgets\Type\Enum\SidebarEnum;

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
        $this->loadDependecies();

        add_action('wp_loaded', [$this, 'register']);
        add_action('graphql_register_types', [$this, 'registerFields']);
        add_filter('graphql_data_loaders', [$this, 'registerLoader'], 10, 2);
    }

    private function loadDependecies()
    {
        // phpcs:ignore
        require_once __DIR__ . '/../vendor/autoload.php';
    }

    public function register()
    {
        Registry::init();
    }

    public function registerLoader( $loaders, AppContext $context )
    {
        $loaders['widget'] = new WidgetLoader($context);
        $loaders['sidebar'] = new SidebarLoader($context);
        return $loaders;
    }

    public function registerFields()
    {
        register_graphql_field(
            'RootQuery',
            'widget',
            [
                'type' => 'WidgetInterface',
                'description' => __('Example field added to the RootQuery Type', 'replace-with-your-textdomain'),
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
                'description' => __('Example field added to the RootQuery Type', 'replace-with-your-textdomain'),
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

        register_graphql_connection(
            [
            'fromType' => 'RootQuery',
            'toType' => 'WidgetInterface',
            'fromFieldName' => 'widgets',
            'connectionArgs' => [
                'sidebar' => [
                    'type' => 'SidebarEnum'
                ],
            ],
            'resolve' => function ( $root, $args, $context, $info ) {
                $resolver = new WidgetConnectionResolver($root, $args, $context, $info);
                return $resolver->get_connection();
            }
            ]
        );

        register_graphql_connection(
            [
            'fromType' => 'RootQuery',
            'toType' => 'Sidebar',
            'fromFieldName' => 'sidebars',
            'connectionArgs'        => [
                'sidebar' => [
                    'type' => [
                        'non_null' => 'SidebarEnum'
                    ]
                ]
            ],
            'resolve' => function ( $sidebar, $args, $context, $info ) {
                $resolver = new SidebarConnectionResolver($sidebar, $args, $context, $info);
                return $resolver->get_connection();
            }
            ]
        );

        register_graphql_connection(
            [
                    'fromType' => 'Sidebar',
                    'toType' => 'WidgetInterface',
                    'fromFieldName' => 'widgets',
                    'resolve'  => function ( $sidebar, $args, $context, $info ) {
                        $resolver = new WidgetConnectionResolver($sidebar, $args, $context, $info);
                        $resolver->set_query_arg('sidebar', $sidebar->databaseId);

                        return $resolver->get_connection();
                    },
            ]
        );

        register_graphql_connection(
            [
                    'fromType' => 'WidgetInterface',
                    'toType' => 'Sidebar',
                    'fromFieldName' => 'sidebar',
                    'oneToOne' => true,
                    'resolve'  => function ( $widget, $args, $context, $info ) {
                        $sidebarId = Registry::init()->getSidebarIdByInstanceId($widget->databaseId);
                        $resolver = new SidebarConnectionResolver($widget, $args, $context, $info);
                        $resolver->set_query_arg('sidebar', $sidebarId);

                        return $resolver->one_to_one()->get_connection();
                    },
            ]
        );

        SidebarEnum::register();
        WPNavMenuWidget::register();
        WPWidgetRecentComments::register();
        WPMediaImageWidget::register();
    }
}
