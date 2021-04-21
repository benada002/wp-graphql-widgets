<?php

namespace WPGraphQLWidgets\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
use WP_Widget;
use WPGraphQLWidgets\Registry;

class Widget extends Model
{
    protected $key;

    public $type;

    public $index;

    protected $data;

    protected $instance;

    /**
     * Widget constructor.
     *
     * @param WP_Widget $widget The incoming WP_Widget to be modeled
     *
     * @return void
     * @throws \Exception
     */
    public function __construct( WP_Widget $widget, String $key )
    {
        $splitedKey = explode('|', $key);

        $this->type = $splitedKey[0];
        $this->index = (int) ($splitedKey[1]);
        $this->data = $widget->get_settings()[$this->index];
        $this->key = $key;
        $this->instance = $widget ?? null;

        parent::__construct();
    }

    protected function init()
    {
        if (empty($this->fields) ) {
            $this->fields = array_merge(
                $this->data,
                [
                    'id' => function () {
                        return ( ! empty($this->key) ) ? Relay::toGlobalId('Widget', $this->key) : null;
                    },
                    'type' => function () {
                        return ( ! empty($this->type) ) ? $this->type : null;
                    },
                    'title' => function () {
                        return $this->data['title'] ?? null;
                    },
                    'idBase' => function () {
                        return $this->instance->id_base ?? null;
                    },
                    'html' => function () {
                        ob_start();
                        the_widget($this->type, $this->instance);
                        $html = ob_get_clean();

                        return $html ?? null;
                    },
                    'sidebarName' => function () {
                        $sidebar = Registry::init()->getSidebarByInstanceId($this->instance->id);
                        $sidebarName = $sidebar['name'];

                        return $sidebarName ?? null;
                    },
                ]
            );

        }
    }

    protected function is_private()
    {
        $sidebar = Registry::init()->getSidebarIdByInstanceId($this->instance->id);

        if ($sidebar !== null && $sidebar !== 'wp_inactive_widgets') {
            return false;
        }

        return current_user_can('edit_theme_options');
    }
}
