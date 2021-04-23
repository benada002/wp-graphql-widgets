<?php

use WPGraphQL\AppContext;
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
    public static function init()
    {
        add_action('wp_loaded', [__CLASS__, 'register']);
        add_action('graphql_register_types', [__CLASS__, 'registerFields']);
        add_filter('graphql_data_loaders', [__CLASS__, 'registerLoader'], 10, 2);
    }

    public static function register()
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
            'RootQuery', 'widget', [
            'type' => 'WidgetInterface',
            'description' => __('Example field added to the RootQuery Type', 'replace-with-your-textdomain'),
            'resolve' => function ( $root, $args, $context, $info ) {
                return [
                    'id' => 'test',
                ];
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
                ]
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
    }
}
