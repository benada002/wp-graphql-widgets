<?php
namespace WPGraphQLWidgets;

use WPGraphQLWidgets\Type\Widget;
use WPGraphQLWidgets\Type\WidgetTypes;
use WPGraphQLWidgets\Type\Union\WidgetUnion;

class Registry
{
    public static function init()
    {
        WidgetUnion::register();
        WidgetTypes::register();
        Widget::register();
    }
}
