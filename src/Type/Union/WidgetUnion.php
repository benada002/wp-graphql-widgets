<?php

namespace WPGraphQLWidgets\Type\Union;

use WPGraphQLWidgets\Type\WidgetTypes;

class WidgetUnion
{
    public static function register()
    {
        \register_graphql_union_type(
            'WidgetUnion', [
                'typeNames' => WidgetTypes::getWidgetTypeNames(),
                'resolveType' => function ($widget) {
                    if (!isset($widget['type'])) {
                        return null;
                    }

                    return graphql_format_type_name($widget['type']);
                }
            ]
        );
    }
}
