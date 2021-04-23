<?php

namespace WPGraphQLWidgets\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WP_Widget;
use WPGraphQLWidgets\Registry;

class Sidebar extends Model
{
    protected $key;

    protected $data;

    /**
     * Widget constructor.
     *
     * @param $widget The incoming WP_Widget to be modeled
     *
     * @return void
     * @throws \Exception
     */
    public function __construct( $sidebar, $key )
    {
        $this->key = $key;
        $this->data = $sidebar;

        parent::__construct();
    }

    protected function init()
    {
        if (empty($this->fields) ) {
            foreach ($this->data as $key => $value) {
                $this->fields[\graphql_format_field_name($key)] = $value;
            }

            $this->fields = array_merge(
                $this->fields,
                [
                    'id' => function () {
                        return ( ! empty($this->key) ) ? Relay::toGlobalId('Sidebar', $this->key) : null;
                    },
                    'databaseId' => function () {
                        return ( ! empty($this->key) ) ? $this->key : null;
                    }
                ]
            );
        }
    }

    protected function is_private()
    {
        return $this->key === 'wp_inactive_widgets';
    }
}
