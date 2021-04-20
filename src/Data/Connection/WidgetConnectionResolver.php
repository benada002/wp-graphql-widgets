<?php

namespace WPGraphQLWidgets\Data\Connection;

use WPGraphQL\Data\Connection\AbstractConnectionResolver;

class WidgetConnectionResolver extends AbstractConnectionResolver
{
    public function get_offset()
    {
        $offset = null;
        return $offset;
    }

    public function get_ids()
    {

        $ids     = [];
        $queried = $this->get_query();

        if (empty($queried) ) {
            return $ids;
        }

        foreach ($queried as $key => $item) {
            $ids[$key] = $item;
        }

        return $ids;

    }

    public function get_query_args()
    {
        //return $this->query_args;
    }

    public function get_query()
    {
        $widgets = [];
        global $wp_widget_factory;

        foreach ($wp_widget_factory->widgets as $widgetTypeKey => $widgetType) {
            $widgetTypeSettings = $widgetType->get_settings();

            if (!is_array($widgetTypeSettings) || empty($widgetTypeSettings)) {
                continue;
            }

            foreach ($widgetTypeSettings as $key => $widget) {
                if (is_array($widget) && !empty($widget)) {
                    $widgets["$widgetTypeKey|$key"] = $widget;
                }
            }
        }

        return array_keys($widgets);
    }

    public function get_loader_name()
    {
        return 'widget';
    }

    public function is_valid_offset( $offset )
    {
        return true;
    }

    public function should_execute()
    {
        return true;
    }

    public function get_nodes()
    {

        $nodes = parent::get_nodes();

        if (isset($this->args['after']) ) {
            $key   = array_search($this->get_offset(), array_keys($nodes), true);
            $nodes = array_slice($nodes, $key + 1, null, true);
        }

        if (isset($this->args['before']) ) {
            $nodes = array_reverse($nodes);
            $key   = array_search($this->get_offset(), array_keys($nodes), true);
            $nodes = array_slice($nodes, $key + 1, null, true);
            $nodes = array_reverse($nodes);
        }

        $nodes = array_slice($nodes, 0, $this->query_amount, true);

        return ! empty($this->args['last']) ? array_filter(array_reverse($nodes, true)) : $nodes;
    }
}
