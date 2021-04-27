<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQL\Data\DataSource;

class WPWidgetCategories
{
    public static function register()
    {
        register_graphql_connection(
            [
                    'fromType' => 'WPWidgetCategories',
                    'toType' => 'Category',
                    'fromFieldName' => 'rendered',
                    'resolve'  => function ( $widget, $args, $context, $info ) {
                        return DataSource::resolve_term_objects_connection($widget, $args, $context, $info, 'category');
                    },
            ]
        );
    }
}
