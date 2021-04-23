<?php

namespace WPGraphQLWidgets\Data\Loader;

use WPGraphQL\Data\Loader\AbstractDataLoader;
use WPGraphQLWidgets\Model\Sidebar;
use WPGraphQLWidgets\Registry;

class SidebarLoader extends AbstractDataLoader
{
    protected function get_model( $entry, $key )
    {
        return new Sidebar($entry, $key);
    }

    public function loadKeys( array $keys )
    {
        $sidebars = Registry::init()->getSidebars();
        $loaded = [];

        if (!is_array($sidebars) || empty($sidebars) ) {
            return $loaded;
        }

        foreach ( $keys as $key ) {
            $loaded[ $key ] = null;

            if (isset($sidebars[$key])) {
                $loaded[$key] = $sidebars[$key];
            }
        }

        return $loaded;
    }
}
