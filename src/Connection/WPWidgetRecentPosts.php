<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQL\Data\DataSource;

class WPWidgetRecentPosts
{
    public static function register()
    {
        register_graphql_connection(
            [
                    'fromType' => 'WPWidgetRecentPosts',
                    'toType' => 'Post',
                    'fromFieldName' => 'rendered',
                    'resolve'  => function ( $widget, $args, $context, $info ) {
                        $args['orderby'] = 'DATE';
                        $args['order'] = 'DESC';
                        $args['first'] = absint($widget->number);

                        return DataSource::resolve_post_objects_connection($widget, $args, $context, $info, 'post');
                    },
            ]
        );
    }
}
