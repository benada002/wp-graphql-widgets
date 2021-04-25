<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQLWidgets\Data\Connection\SidebarConnectionResolver;
use WPGraphQLWidgets\Registry;

class Sidebar
{
    public static function register()
    {
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
    }
}