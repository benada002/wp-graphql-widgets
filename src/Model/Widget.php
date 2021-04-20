<?php

namespace WPGraphQLWidgets\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WP_Widget;

class Widget extends Model
{
    protected $key;
    protected $data;

    /**
     * Widget constructor.
     *
     * @param WP_Widget $widget The incoming WP_Theme to be modeled
     *
     * @return void
     * @throws \Exception
     */
    public function __construct( $widget, String $key )
    {
        $this->data = $widget;
        $this->key = $key;
        parent::__construct();
    }

    protected function init()
    {
        if (empty($this->fields) ) {

            $this->fields = [
                'id' => function () {
                    return ( ! empty($this->key) ) ? Relay::toGlobalId('Widget', $this->key) : null;
                },
                'type' => function () {
                    $type = explode('|', $this->key);
                    return ( ! empty($type[0]) ) ? $type[0] : null;
                },
                'settings' => function () {
                    $this->data['type'] = explode('|', $this->key)[0];
                    return $this->data;
                },
            ];

        }
    }
}
