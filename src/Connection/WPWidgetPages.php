<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

class WPWidgetPages
{
    public static function register()
    {
        register_graphql_connection(
            [
                    'fromType' => 'WPWidgetPages',
                    'toType' => 'Page',
                    'fromFieldName' => 'rendered',
                    'resolve'  => function ( $widget, $args, $context, $info ) {
                        $resolver = new PostObjectConnectionResolver( $widget, $args, $context, $info, 'page' );
                        switch ($widget->sortby) {
                            case 'post_title':
                                $resolver->set_query_arg( 'orderby', 'title' );
                                break;
                            case 'menu_order':
                                $resolver->set_query_arg( 'orderby', 'menu_order' );
                                break;
                            default:
                                $resolver->set_query_arg( 'orderby', 'ID' );
                        }

                        if (!empty($widget->exclude)) {
                            $resolver->set_query_arg( 'post__not_in', explode(',', $widget->exclude) );
                        }         

                        return $resolver->get_connection();
                    },
            ]
        );
    }
}
