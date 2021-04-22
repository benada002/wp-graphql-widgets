<?php

namespace WPGraphQLWidgets\Type\InterfaceType;

use WPGraphQLWidgets\Registry;

class WidgetInterface
{
    public static function register()
    {
        \register_graphql_interface_type(
            'WidgetInterface',
            [
                'fields' => [
                    'id' => [
                        'description' => __('Unique identifier for the object.'),
                        'type'        => [
                            'non_null' => 'ID'
                        ],
                    ],
                    'type' => [
                        'description' => __('Type of Widget for the object.'),
                        'type'        => 'String',
                    ],
                    'title' => [
                        'description' => __('The title for the object.'),
                        'type'        => 'String',
                    ],
                    'idBase' => [
                        'description' => __('The HTML for the object.'),
                        'type'        => 'String',
                    ],
                    'html' => [
                        'description' => __('The HTML for the object.'),
                        'type'        => 'String',
                    ],
                    'active' => [
                        'description' => __('Shows if widget is active.'),
                        'type'        => 'Boolean',
                    ],
                ],
                'resolveType' => function ($widget) {
                    if (!isset($widget->type)) {
                        return null;
                    }

                    return Registry::init()->getWidgetTypeNameByKey($widget->type);
                }
            ]
        );

        \register_graphql_interfaces_to_types(['WidgetInterface'], Registry::init()->getWidgetTypeNames());
    }
}
