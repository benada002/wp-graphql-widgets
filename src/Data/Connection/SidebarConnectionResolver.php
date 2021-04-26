<?php

namespace WPGraphQLWidgets\Data\Connection;

use GraphQLRelay\Relay;
use WPGraphQL\Data\Connection\AbstractConnectionResolver;
use WPGraphQLWidgets\Registry;

class SidebarConnectionResolver extends AbstractConnectionResolver
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
        $sidebars = Registry::init()->getSidebars();
        $queryArgs = $this->query_args;

        foreach ($sidebars as $sidebarId => $sidebar) {
            if (isset($queryArgs['sidebar'])
                && $queryArgs['sidebar'] !== $sidebarId
            ) {
                unset($sidebars[$sidebarId]);
            }
        }

        return array_keys($sidebars);
    }

    public function get_loader_name()
    {
        return 'sidebar';
    }

    public function is_valid_offset( $offset )
    {
        $sidebars = Registry::init()->getSidebars();

        return isset($sidebars[$offset]) && ! empty($sidebars[$offset]);
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
