<?php

namespace WPGraphQLWidgets\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQLWidgets\Model\Widget;
use GraphQLRelay\Relay;

class WidgetLoader extends AbstractDataLoader
{
    protected $shouldCache = false;

    protected function get_model( $entry, $key )
    {
        return new Widget($entry, $key);
    }

    public function loadKeys( array $keys )
    {
        global $wp_widget_factory;
        $widgets = $wp_widget_factory->widgets;
        $loaded = [];

        if (!is_array($widgets) || empty($widgets) ) {
            return $loaded;
        }

        foreach ( $keys as $key ) {

            $loaded[ $key ] = null;
            $keyArray = explode('|', $key);
            $widgetType = $widgets[$keyArray[0]];

            if (count($keyArray) !== 2 || empty($widgetType)) {
                continue;
            }

            $widgetTypeSettings = $widgets[$keyArray[0]]->get_settings();

            if (!is_array($widgetTypeSettings)) {
                continue;
            }

            $widgetSettings = $widgetTypeSettings[$keyArray[1]];

            if (!empty($widgetSettings)) {
                $loaded[$key] = $widgetSettings;
            }
        }

        return $loaded;
    }
}
