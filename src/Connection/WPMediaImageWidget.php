<?php

namespace WPGraphQLWidgets\Connection;

use WPGraphQL\Data\Connection\PostObjectConnectionResolver;

class WPMediaImageWidget
{
    public static function register()
    {
        register_graphql_connection(
            [
                'fromType' => 'WPWidgetMediaImage',
                'toType' => 'MediaItem',
                'fromFieldName' => 'rendered',
                'oneToOne'      => true,
                'resolve' => function ( $widget, $args, $context, $info ) {
                    if ( empty( $widget->attachmentId) ) {
                        return null;
                    }
    
                    $resolver = new PostObjectConnectionResolver( $widget, $args, $context, $info, 'attachment' );
                    $resolver->set_query_arg( 'p', absint( $widget->attachmentId ) );

                    return $resolver->one_to_one()->get_connection();
                },
              ]
        );
    }
}
