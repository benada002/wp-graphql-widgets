<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQL\Data\Connection\MenuConnectionResolver;

class WPNavMenuWidget
{
    public static function register()
    {
        register_graphql_connection(
            [
                'fromType' => 'WPNavMenuWidget',
                'toType' => 'Menu',
                'fromFieldName' => 'rendered',
                'oneToOne'      => true,
                'resolve' => function ( $widget, $args, $context, $info ) {
                    $resolver = new MenuConnectionResolver($widget, $args, $context, $info);
                    $resolver->set_query_arg('include', $widget->nav_menu);
                    return $resolver->one_to_one()->get_connection();
                },
              ]
        );
    }
}
