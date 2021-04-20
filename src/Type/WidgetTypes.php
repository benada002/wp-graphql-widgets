<?php

namespace WPGraphQLWidgets\Type;

class WidgetTypes
{
    private static $widgetTypes = null;

    public static function register()
    {
        $widgets = self::getWidgetTypes();

        foreach ($widgets as $name => $fields) {
            \register_graphql_object_type(
                $name, [
                'fields' => $fields,
                ]
            );
        }
    }

    public static function getWidgetTypes()
    {
        self::registerWidgetTypes();

        return self::$widgetTypes;
    }

    public static function getWidgetTypeNames()
    {
        self::registerWidgetTypes();

        return array_keys(self::$widgetTypes);
    }

    private static function registerWidgetTypes()
    {
        if (self::$widgetTypes !== null) {
            return;
        }

        global $wp_widget_factory;
        $widgets = $wp_widget_factory->widgets;

        foreach ($widgets as $name => $widget) {
            $objectName = \graphql_format_type_name($name);
            $settings = $widget->get_settings();

            if (empty($settings)) {
                continue;
            }

            foreach ($settings as $setting) {
                if (count($setting) > 0) {
                    $fields = self::$widgetTypes[$objectName] = array_map(
                        function ($setting) {
                            return [
                                'description' => 'Widget',
                                'type' => self::getFieldType($setting),

                            ];
                        },
                        $setting
                    );

                    if (!in_array($objectName, self::$widgetTypes) && count($fields) > 0) {
                        self::$widgetTypes[$objectName] = $fields;
                    }

                    break;
                }
            }
        }
    }

    private static function getFieldType($setting)
    {
        switch(gettype($setting)) {

        case 'string':
            return 'String';
            break;

        case 'integer':
            return 'Int';
            break;

        case 'double':
            return 'Float';
            break;

        case 'boolean':
            return 'Boolean';
            break;
                        
        default:
            return null;

        }
    }
}
