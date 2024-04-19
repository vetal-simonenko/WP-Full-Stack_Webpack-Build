<?php
// Theme sidebars

function theme_widget_init() {
  register_sidebar( array(
    'id'            => 'default-sidebar',
    'name'          => __( 'Default Sidebar', 'base' ),
    'before_widget' => '<div class="widget %2$s" id="%1$s">',
    'after_widget'  => '</div>',
    'before_title'  => '<h3>',
    'after_title'   => '</h3>'
  ) );
}
add_action( 'widgets_init', 'theme_widget_init' );
