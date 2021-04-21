<?php

namespace WPGraphQLWidgets\Data\Connection;

use WPGraphQL\Data\Connection\AbstractConnectionResolver;

class WidgetConnectionResolver extends AbstractConnectionResolver
{
    public function get_offset()
    {
        $offset = null;
        if (! empty($this->args['after']) ) {
            $offset = substr(base64_decode($this->args['after']), strlen('arrayconnection:'));
        } elseif (! empty($this->args['before']) ) {
            $offset = substr(base64_decode($this->args['before']), strlen('arrayconnection:'));
        }
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
        return $this->query_args;
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
        if (empty($this->ids) ) {
            return [];
        }

        $nodes = [];

        $ids = $this->ids;

        $ids = ! empty($this->args['last']) ? array_reverse($ids) : $ids;

        foreach ( $ids as $id ) {
            $model = $this->get_node_by_id($id);
            if (true === $this->is_valid_model($model) ) {
                $nodes[ $id ] = $model;
            }
        }

        if (! empty($this->get_offset()) ) {
            $key = array_search($this->get_offset(), array_keys($nodes), true);

            if (false !== $key ) {
                $key = absint($key);
                if ((! empty($this->args['before']) && ! empty($this->args['last']))
                    || (! empty($this->args['after']) && ! empty($this->args['first']))
                ) {
                    $key ++;
                    $nodes = array_slice($nodes, $key, null, true);
                } elseif ((! empty($this->args['after']) && ! empty($this->args['last']))
                    || (! empty($this->args['before']) && ! empty($this->args['first']))
                ) {
                    $nodes = array_slice($nodes, 0, $key, true);
                }
            }
        }

        return array_slice($nodes, 0, $this->query_amount, true);
    }
}
