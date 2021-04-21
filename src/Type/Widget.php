<?php

namespace WPGraphQLWidgets\Type;

class Widget
{
    public static function register()
    {
        \register_graphql_object_type(
            'Widget',
            [
                'type'  => 'WidgetUnion',
                'fields' => [
                    'id' => [
                        'description' => __('Unique identifier for the object.'),
                        'type'        => 'String',
                    ],
                    'type' => [
                        'description' => __('Type of Widget for the object.'),
                        'type'        => 'String',
                    ],
                    'title' => [
                        'description' => __('The title for the object.'),
                        'type'        => 'String',
                    ],
                ],
            ]
        );
    }
}
