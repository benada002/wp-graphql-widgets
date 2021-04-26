<?php

namespace WPGraphQLWidgets\Data\Connection;

use GraphQLRelay\Relay;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQLWidgets\Registry;

class WidgetConnectionResolver extends AbstractConnectionResolver
{
    public function get_offset()
    {
        $offset = null;
        if (! empty($this->args['after']) ) {
            $offset = Relay::fromGlobalId($this->args['after'])['id'];
        } elseif (! empty($this->args['before']) ) {
            $offset = Relay::fromGlobalId($this->args['before'])['id'];
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
        $queryArgs = [];

        foreach ($this->args['where'] as $key => $arg) {
            if (!empty($arg)) {
                $queryArgs[$key] = $arg;
            }
        }

        return $queryArgs;
    }

    public function get_query()
    {
        $widgets = Registry::init()->getWidgets();
        $queryArgs = $this->query_args;

        foreach ($widgets as $key => $widget) {
            if (isset($queryArgs['sidebar'])
                && !empty(Registry::init()->getSidebarIdByInstanceId($queryArgs['sidebar']))
            ) {
                unset($widgets[$key]);
            }
        }

        return $this->maybeSortBySidebarOrder($widgets);
    }

    private function maybeSortBySidebarOrder($widgets)
    {
        if (!isset($this->query_args['sidebar'])
            && !Registry::init()->getSidebarWidgetsById($this->query_args['sidebar'])
        ) {
            return array_keys($widgets);
        }

        $order = Registry::init()->getSidebarWidgetsById($this->query_args['sidebar']);
        $widgetKeys = [];

        foreach ($order as $key) {
            if (isset($widgets[$key])) {
                $widgetKeys[] = $key;
            }

        }

        return $widgetKeys;
    }

    public function get_loader_name()
    {
        return 'widget';
    }

    public function is_valid_offset( $offset )
    {
        $widgets = Registry::init()->getWidgets();

        return isset($widgets[$offset]) && ! empty($widgets[$offset]);
    }

    public function has_next_page() {
        if (empty($this->ids)) {
            return false;
        }

		$key = array_search($this->get_offset(), $this->ids, true);
        $length = count($this->ids);
        $nextIndex = $key + $this->query_amount + 1;

		if ( ! empty( $this->args['after'] ) ) {
			return $nextIndex < $length;
		}

		return $key < $length;
	}

    public function has_previous_page() {
        if (empty($this->ids)) {
            return false;
        }

        $key = array_search($this->get_offset(), $this->ids, true);

        if ( ! empty( $this->args['before'] ) ) {
			return $key - $this->query_amount > 0;
		}

		return $key > 0;
	}

    public function should_execute()
    {
        return !(
            isset($this->query_args['sidebar'])
            && $this->query_args['sidebar'] === 'wp_inactive_widgets'
            && !current_user_can('edit_theme_options')
        );
    }

    public function get_nodes()
    {
        if (empty($this->ids) ) {
            return [];
        }

        $key = null;
        $nodes = [];
        $ids = $this->ids;

        foreach ( $ids as $id ) {
            $model = $this->get_node_by_id($id);
            if (true === $this->is_valid_model($model) ) {
                $nodes[ $id ] = $model;
            }
        }

        if ((! empty($this->args['before']))) {
            $nodes = array_reverse($nodes);
            $key = array_search($this->get_offset(), array_keys($nodes), true);
            $nodes = $key !== false ? array_slice($nodes, absint($key) + 1, null, true) : [];
			$nodes = array_reverse($nodes);
        } elseif ((! empty($this->args['after']))) {
            $key = array_search($this->get_offset(), array_keys($nodes), true);
            $nodes = $key !== false ? array_slice($nodes, absint($key) + 1, null, true) : [];
        }



        if (
            (! empty($this->args['before']))
            || (! empty($this->args['last']) && empty($this->args['after']))
        ) {
            $nodes = array_reverse($nodes);
            $nodes = array_slice($nodes, 0, $this->query_amount, true);
            return array_reverse($nodes);
        }

		return array_slice($nodes, 0, $this->query_amount, true);
    }
}
