<?php

namespace WPGraphQLWidgets\Type;

class Widget
{
    public static function register()
    {
        $fields = [
            'id' => [
                'description' => __('Unique identifier for the object.'),
                'type'        => 'String',
            ],
            'type' => [
                'description' => __('Type of Widget for the object.'),
                'type'        => 'String',
            ],
            'settings' => [
                'description' => __('The title for the object.'),
                'type'        => 'WidgetUnion',
            ]
        ];

        \register_graphql_object_type('Widget', ['fields' => $fields]);
    }
}
