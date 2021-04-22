<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQL\Data\DataSource;

class WPWidgetRecentComments
{
    public static function register()
    {
        register_graphql_connection(
            [
                    'fromType' => 'WPWidgetRecentComments',
                    'toType' => 'Comment',
                    'fromFieldName' => 'rendered',
                    'resolve'  => function ( $widget, $args, $context, $info ) {
                        $args['orderby'] = 'COMMENT_DATE';
                        $args['order'] = 'DESC';
                        $args['first'] = absint($widget->number);

                        return DataSource::resolve_comments_connection($widget, $args, $context, $info);
                    },
                ]
        );
    }
}
