<?php

namespace WPGraphQLWidgets\Type\ObjectType;

class Sidebar
{
    public static function register()
    {
        \register_graphql_object_type(
            'Sidebar',
            [
                'fields' => [
                    'id' => [
                        'description' => __('Unique identifier of the sidebar.'),
                        'type'        => 'ID',
                    ],
                    'name' => [
                        'description' => __('The name of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'description' => [
                        'description' => __('The description of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'class' => [
                        'description' => __('The class of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'beforeWidget' => [
                        'description' => __('The before_widget option of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'afterWidget' => [
                        'description' => __('The after_widget option of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'beforeTitle' => [
                        'description' => __('The before_title option of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'afterTitle' => [
                        'description' => __('The after_title option of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'beforeSidebar' => [
                        'description' => __('The before_sidebar option of the sidebar.'),
                        'type'        => 'String',
                    ],
                    'afterSidebar' => [
                        'description' => __('The after_sidebar option of the sidebar.'),
                        'type'        => 'String',
                    ],
                ],
            ]
        );
    }
}
