<?php
namespace WPGraphQLWidgets;

use WPGraphQLWidgets\Type\ObjectType\Sidebar;
use WPGraphQLWidgets\Type\WidgetTypes;
use WPGraphQLWidgets\Type\InterfaceType\WidgetInterface;

class Registry
{
    private static $instance;
    private $widgetTypes = [];
    private $activeWidgets = [];
    private $sidebarWidgets = [];
    private $sidebars = [];  

    public static function init()
    {
        if (isset(self::$instance) || self::$instance instanceof Registry ) {
            return self::$instance;
        }
        self::$instance = new Registry();
        self::$instance->registerActiveWidgets();
        self::$instance->registerSidebarWidgets();
        self::$instance->registerSidebars();
        
        do_action('register_widget_types', self::$instance);
        WidgetTypes::register();

        Sidebar::register();
        WidgetInterface::register();
    }

    public function getSidebarWidgets()
    {
        return $this->sidebarWidgets;
    }

    public function getSidebars()
    {
        return $this->sidebars;
    }

    public function getActiveWidgets()
    {
        $this->registerActiveWidgets();

        return $this->activeWidgets;
    }

    public function getSidebarByInstanceId( $instanceId )
    {
        $id = $this->getSidebarIdByInstanceId($instanceId);
        $sidebar = $this->sidebars[$id];
        $sidebar['id'] = $id;

        return $sidebar;
    }

    public function getSidebarIdByInstanceId( $instanceId )
    {
        foreach ( $this->sidebarWidgets as $id => $widgets ) {
            if (in_array($instanceId, $widgets) ) {
                return $id;
            }
        }

        return null;
    }

    public function getWidgetTypeNames()
    {
        return array_values($this->widgetTypes);
    }

    public function getWidgetTypeNameByKey($key)
    {
        if (!isset($this->widgetTypes[$key])) {
            return null;
        }

        return $this->widgetTypes[$key];
    }

    private function registerActiveWidgets()
    {
        if (! empty($this->activeWidgets)) {
            return;
        }

        global $wp_widget_factory;
        $activeWidgets = $wp_widget_factory->widgets;

        foreach ($activeWidgets as $name => $widget) {
            $settings = $widget->get_settings();

            if (empty($settings)) {
                continue;
            }

            foreach ($settings as $setting) {
                if (!in_array($name, $this->activeWidgets) && !empty($setting)) {
                    $this->activeWidgets[$name] = $setting;
                    break;
                }
            }
        }
    }

    private function registerSidebarWidgets()
    {
        if (! empty($this->sidebarWidgets)) {
            return;
        }

        $this->sidebarWidgets = wp_get_sidebars_widgets();
    }

    private function registerSidebars()
    {
        if (! empty($this->sidebars)) {
            return;
        }
        global $wp_registered_sidebars;

        $this->sidebars = $wp_registered_sidebars;
    }

    public function registerWidgetType($typeName, $config)
    {
        if (in_array($typeName, $this->widgetTypes)) {
            return;
        }

        $objectName = \graphql_format_type_name($typeName);
        $this->widgetTypes[$typeName] = $objectName;

        \register_graphql_object_type($objectName, $config);
    }
}
