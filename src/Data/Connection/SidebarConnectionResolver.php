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
                    || (! empty($this->args['after']) && empty($this->args['last']))
                ) {
                    $key ++;
                    $nodes = array_slice($nodes, $key, null, true);
                } elseif ((! empty($this->args['after']) && ! empty($this->args['last']))
                    || (! empty($this->args['before']) && empty($this->args['last']))
                ) {
                    $nodes = array_slice($nodes, 0, $key, true);
                }
            }
        }

        return array_slice($nodes, 0, $this->query_amount, true);
    }
}
