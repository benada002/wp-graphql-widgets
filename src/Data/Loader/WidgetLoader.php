<?php

namespace WPGraphQLWidgets\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQLWidgets\Model\Widget;
use GraphQLRelay\Relay;
use WPGraphQLWidgets\Registry;

class WidgetLoader extends AbstractDataLoader
{
    protected function get_model( $entry, $key )
    {
        return new Widget($entry, $key);
    }

    public function loadKeys( array $keys )
    {
        $widgets = Registry::init()->getWidgets();
        $loaded = [];

        if (!is_array($widgets) || empty($widgets) ) {
            return $loaded;
        }

        foreach ( $keys as $key ) {
            $loaded[ $key ] = null;

            if (isset($widgets[$key])) {
                $loaded[$key] = $widgets[$key];
            }
        }

        return $loaded;
    }
}
