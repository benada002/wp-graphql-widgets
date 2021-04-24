<?php

namespace WPGraphQLWidgets\Model;

use GraphQLRelay\Relay;
use WPGraphQL\Model\Model;
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
     * @param $widget The incoming WP_Widget to be modeled
     *
     * @return void
     * @throws \Exception
     */
    public function __construct( $widget, String $key )
    {
        $this->type = get_class($widget);
        $this->index = absint(str_replace($widget->id_base . '-', '', $key));
        $this->data = $widget->get_settings()[$this->index];
        $this->key = $key;
        $this->instance = $widget;

        if (empty($this->data)) {
            $this->data = [
                'id' => '',
            ];
        }

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
                        return ( ! empty($this->key) ) ? Relay::toGlobalId('WidgetInterface', $this->key) : null;
                    },
                    'databaseId' => function () {
                        return ( ! empty($this->key) ) ? $this->key : null;
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
                    'active' => function () {
                        return $this->is_active();
                    },
                ]
            );
        }
    }

    public function is_active()
    {
        $sidebar = Registry::init()->getSidebarIdByInstanceId($this->instance->id);

        return $sidebar !== null && $sidebar !== 'wp_inactive_widgets';
    }

    protected function is_private()
    {
        return !$this->is_active() && !current_user_can('edit_theme_options');
    }
}
