<?php
// Custom breadcrumb navigation builder for improved user experience and SEO.
function custom_breadcrumbs() {
    global $wp_query, $post, $paged;
    $on_front   = get_option( 'show_on_front' );
    $blog_page  = get_option( 'page_for_posts' );
    $link       = apply_filters( 'custom_breadcrumb_link', '<li><a  href="%1$s" title="%2$s" rel="bookmark">%2$s</a> </li>  ' );
    $current    = apply_filters( 'custom_breadcrumb_current', '<li><span>%s</span></li>' );
    if ( ( $on_front == 'page' && is_front_page() ) || ( $on_front == 'posts' && is_home() ) ) {
        return;
    }
    $out = '';
    if ( $on_front == "page" && is_home() ) {
        $blog_title = isset( $blog_page ) ? get_the_title( $blog_page ) : 'Our Blog';
        $out .= sprintf( $link, home_url(), 'Home' ) . $separator . sprintf( $current, $blog_title );
    } else {
        $out .= sprintf( $link, home_url(), 'Home' );
    }
    if ( is_singular() ) {
        if ( is_singular( 'post' ) && $blog_page > 0 ) {
            $out .= $separator . sprintf( $link, get_permalink( $blog_page ), get_the_title( $blog_page ) );
        }
        if ( $post->post_parent > 0 ) {
            $ancestors = isset( $post->ancestors ) ? (array) $post->ancestors : array( $post->post_parent );
            foreach ( array_reverse( $ancestors ) as $value ) {
                $out .= $separator . sprintf( $link, get_permalink( $value ), get_the_title( $value ) );
            }
        }
        $post_type = get_post_type();
        if ( get_post_type_archive_link( $post_type ) ) {
            $post_type_obj = get_post_type_object( get_post_type($post) );
            $out .= $separator . sprintf( $link, get_post_type_archive_link( $post_type ), $post_type_obj->labels->menu_name );
        }
        $out .= $separator . sprintf( $current, get_the_title() );
    } else {
        if ( is_post_type_archive() ) {
            $post_type = get_post_type();
            $post_type_obj = get_post_type_object( get_post_type($post) );
            $out .= $separator . sprintf( $current, $post_type_obj->labels->menu_name );
        } else if ( is_tax() ) {
            $out .= $separator . sprintf( $current, $wp_query->queried_object->name );
        } else if ( is_category() ) {
            $out .= $separator . 'Category : ' . sprintf( $current, $wp_query->queried_object->name );
        } else if ( is_tag() ) {
            $out .= $separator . 'Tag : ' . sprintf( $current, $wp_query->queried_object->name );
        } else if ( is_date() ) {
            $out .= $separator;
            if ( is_day() ) {
                global $wp_locale;
                $out .= sprintf( $link, get_month_link( get_query_var( 'year' ), get_query_var( 'monthnum' ) ), $wp_locale->get_month( get_query_var( 'monthnum' ) ).' '.get_query_var( 'year' ) );
                $out .= $separator . sprintf( $current, get_the_date() );
            } else if ( is_month() ) {
                $out .= sprintf( $current, single_month_title( ' ', false ) );
            } else if ( is_year() ) {
                $out .= sprintf( $current, get_query_var( 'year' ) );
            }
        } else if ( is_404() ) {
            $out .= $separator . sprintf( $current, 'Error 404' );
        } else if ( is_search() ) {
            $out .= $separator . sprintf( $current, 'Search' );
        }
    }
    return  '<nav class="site-breadcrumb"><ul>'.$out.'</ul></nav>';
} 