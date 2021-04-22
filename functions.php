<?php

function register_widget_type( string $widget_type, array $config )
{
    add_action(
        'register_widget_types',
        function ( $widget_registry ) use ( $widget_type, $config ) {
            $widget_registry->registerWidgetType($widget_type, $config);
        },
        10
    );
}
