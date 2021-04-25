<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQLWidgets\Data\Connection\WidgetConnectionResolver;

class WidgetInterface {
    public static function register()
    {
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
    }
}