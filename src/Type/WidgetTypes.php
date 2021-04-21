<?php

namespace WPGraphQLWidgets\Type;

use WPGraphQLWidgets\Registry;

class WidgetTypes
{
    public static function register()
    {
        $widgets = Registry::init()->getActiveWidgets();

        foreach ($widgets as $name => $fields) {
            if (! is_array($fields)) {
                continue;
            }

            $config = [];

            foreach ($fields as $key => $field) {
                $config['fields'][$key] = [
                    'type' => self::getFieldType($field),
                ];
            }


            Registry::init()->registerWidgetType($name, $config);
        }
    }

    private static function getFieldType($field)
    {
        switch(gettype($field)) {

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
