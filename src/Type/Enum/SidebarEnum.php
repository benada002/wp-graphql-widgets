<?php

namespace WPGraphQLWidgets\Type\Enum;

use WPGraphQLWidgets\Registry;

class SidebarEnum
{
    public static function register()
    {
        $sidebars = Registry::init()->getSidebars();
        $values = [];

        foreach ($sidebars as $id => $sidebar) {
            $replacedKey = preg_replace('/[^_A-Za-z0-9]/i', '', str_replace(' ', '_',  $sidebar['name']));
            $values[strtoupper($replacedKey)] = $id;
        }

        register_graphql_enum_type(
            'SidebarEnum',
            [
                'description' => __('Sidebar'),
                'values' => $values
            ] 
        );
    }
}
