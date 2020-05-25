<?php
// Default theme settings

//Staging restrictions
if ( file_exists( sys_get_temp_dir() . '/staging-restrictions.php' ) ) {
  define( 'STAGING_RESTRICTIONS', true );
  require_once sys_get_temp_dir() . '/staging-restrictions.php';
}
if ( file_exists( ABSPATH . '/localhost-functions.php' ) ) {
  define( 'LOCALHOST_FUNCTIONS', true );
  require_once ABSPATH . '/localhost-functions.php';
}

function seo_warning() {
  if( get_option( 'blog_public' ) ) return;

  $message = __( 'You are blocking access to robots. You must go to your <a href="%s">Reading</a> settings and uncheck the box for Search Engine Visibility.', 'wordpress' );

  echo '<div class="error"><p>';
  printf( $message, admin_url( 'options-reading.php' ) );
  echo '</p></div>';
}
add_action( 'admin_notices', 'seo_warning' );

function theme_disable_cheks() {
  $disabled_checks = array( 'TagCheck', 'Plugin_Territory', 'CustomCheck', 'EditorStyleCheck' );
  global $themechecks;
  foreach ( $themechecks as $key => $check ) {
    if ( is_object( $check ) && in_array( get_class( $check ), $disabled_checks ) ) {
      unset( $themechecks[$key] );
    }
  }
}
add_action( 'themecheck_checks_loaded', 'theme_disable_cheks' );

add_theme_support( 'automatic-feed-links' );

if ( !isset( $content_width ) ) {
  $content_width = 900;
}

// Clean up wordpres <head>
remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
remove_action('wp_head', 'wp_generator'); // remove wordpress version
remove_action('wp_head', 'feed_links', 2); // remove rss feed links (make sure you add them in yourself if youre using feedblitz or an rss service)
remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links
remove_action('wp_head', 'index_rel_link'); // remove link to index page
remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml (needed to support windows live writer)
remove_action('wp_head', 'start_post_rel_link', 10, 0); // remove random post link
remove_action('wp_head', 'parent_post_rel_link', 10, 0); // remove parent post link
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0); // remove the next and previous post links
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wp_shortlink_wp_head', 10, 0);

function theme_localization () {
  load_theme_textdomain( 'wordpress', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'theme_localization' );

/*
* Let WordPress manage the document title.
* By adding theme support, we declare that this theme does not use a
* hard-coded <title> tag in the document head, and expect WordPress to
* provide it for us.
*/
add_theme_support( 'title-tag' );

//Add [email]...[/email] shortcode
function shortcode_email( $atts, $content ) {
  return antispambot( $content );
}
add_shortcode( 'email', 'shortcode_email' );

//Register tag [template-url]
function filter_template_url( $text ) {
  return str_replace( '[template-url]', get_template_directory_uri(), $text );
}
add_filter( 'the_content', 'filter_template_url' );
add_filter( 'widget_text', 'filter_template_url' );

//Register tag [site-url]
function filter_site_url( $text ) {
  return str_replace( '[site-url]', home_url(), $text );
}
add_filter( 'the_content', 'filter_site_url' );
add_filter( 'widget_text', 'filter_site_url' );

if( class_exists( 'acf' ) && !is_admin() ) {
  add_filter( 'acf/load_value', 'filter_template_url' );
  add_filter( 'acf/load_value', 'filter_site_url' );
}

//Replace standard wp menu classes
function change_menu_classes( $css_classes ) {
  return str_replace( array( 'current-menu-item', 'current-menu-parent', 'current-menu-ancestor' ), 'active', $css_classes );
}
add_filter( 'nav_menu_css_class', 'change_menu_classes' );

//Allow tags in category description
$filters = array( 'pre_term_description', 'pre_link_description', 'pre_link_notes', 'pre_user_description' );
foreach ( $filters as $filter ) {
  remove_filter( $filter, 'wp_filter_kses' );
}

function clean_phone( $phone ){
  return preg_replace( '/[^0-9]/', '', $phone );
}

//Make wp admin menu html valid
function wp_admin_bar_valid_search_menu( $wp_admin_bar ) {
  if ( is_admin() )
    return;

  $form  = '<form action="' . esc_url( home_url( '/' ) ) . '" method="get" id="adminbarsearch"><div>';
  $form .= '<input class="adminbar-input" name="s" id="adminbar-search" tabindex="10" type="text" value="" maxlength="150" />';
  $form .= '<input type="submit" class="adminbar-button" value="' . __( 'Search', 'wordpress' ) . '"/>';
  $form .= '</div></form>';

  $wp_admin_bar->add_menu( array(
    'parent' => 'top-secondary',
    'id'     => 'search',
    'title'  => $form,
    'meta'   => array(
      'class'    => 'admin-bar-search',
      'tabindex' => -1,
    )
  ) );
}

function fix_admin_menu_search() {
  remove_action( 'admin_bar_menu', 'wp_admin_bar_search_menu', 4 );
  add_action( 'admin_bar_menu', 'wp_admin_bar_valid_search_menu', 4 );
}
add_action( 'add_admin_bar_menus', 'fix_admin_menu_search' );

//Disable comments on pages by default
function theme_page_comment_status( $post_ID, $post, $update ) {
  if ( !$update ) {
    remove_action( 'save_post_page', 'theme_page_comment_status', 10 );
    wp_update_post( array(
      'ID' => $post->ID,
      'comment_status' => 'closed',
    ) );
    add_action( 'save_post_page', 'theme_page_comment_status', 10, 3 );
  }
}
add_action( 'save_post_page', 'theme_page_comment_status', 10, 3 );

//custom excerpt
function theme_the_excerpt() {
  global $post;

  if ( trim( $post->post_excerpt ) ) {
    the_excerpt();
  } elseif ( strpos( $post->post_content, '<!--more-->' ) !== false ) {
    the_content();
  } else {
    the_excerpt();
  }
}

//theme password form
function theme_get_the_password_form() {
  global $post;
  $post = get_post( $post );
  $label = 'pwbox-' . ( empty($post->ID) ? rand() : $post->ID );
  $output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
  <p>' . __( 'This content is password protected. To view it please enter your password below:', 'wordpress' ) . '</p>
  <p><label for="' . $label . '">' . __( 'Password:', 'wordpress' ) . '</label> <input name="post_password" id="' . $label . '" type="password" size="20" /> <input type="submit" name="Submit" value="' . esc_attr__( 'Submit', 'wordpress' ) . '" /></p></form>
  ';
  return $output;
}
add_filter( 'the_password_form', 'theme_get_the_password_form' );

function basetheme_options_capability(){
  $role = get_role( 'administrator' );
  $role->add_cap( 'theme_options_view' );
}
add_action( 'admin_init', 'basetheme_options_capability' );

//theme options tab in appearance
if( function_exists( 'acf_add_options_sub_page' ) && current_user_can( 'theme_options_view' ) ) {
  acf_add_options_sub_page( array(
    'title'  => 'Theme Options',
    'parent' => 'themes.php',
  ) );
}

//acf theme functions placeholders
if( !class_exists( 'acf' ) && !is_admin() ) {
  function get_field_reference( $field_name, $post_id ) { return ''; }
  function get_field_objects( $post_id = false, $options = array() ) { return false; }
  function get_fields( $post_id = false ) { return false; }
  function get_field( $field_key, $post_id = false, $format_value = true )  { return false; }
  function get_field_object( $field_key, $post_id = false, $options = array() ) { return false; }
  function the_field( $field_name, $post_id = false ) {}
  function have_rows( $field_name, $post_id = false ) { return false; }
  function the_row() {}
  function reset_rows( $hard_reset = false ) {}
  function has_sub_field( $field_name, $post_id = false ) { return false; }
  function get_sub_field( $field_name ) { return false; }
  function the_sub_field( $field_name ) {}
  function get_sub_field_object( $child_name ) { return false;}
  function acf_get_child_field_from_parent_field( $child_name, $parent ) { return false; }
  function register_field_group( $array ) {}
  function get_row_layout() { return false; }
  function acf_form_head() {}
  function acf_form( $options = array() ) {}
  function update_field( $field_key, $value, $post_id = false ) { return false; }
  function delete_field( $field_name, $post_id ) {}
  function create_field( $field ) {}
  function reset_the_repeater_field() {}
  function the_repeater_field( $field_name, $post_id = false ) { return false; }
  function the_flexible_field( $field_name, $post_id = false ) { return false; }
  function acf_filter_post_id( $post_id ) { return $post_id; }
}

// date archive link
add_action( 'admin_init',
  function (){
    add_settings_section(
      'eg_setting_section',
      __( 'Date archive link', 'wordpress' ),
      function () {},
      'reading'
    );

    add_settings_field(
      'eg_setting_name',
      __( 'Type', 'wordpress' ),
      'eg_setting_callback_function',
      'reading',
      'eg_setting_section'
    );

    register_setting( 'reading', 'eg_date_archive_link_type' );
  }
);

function eg_setting_callback_function(){
  if ( get_option( 'eg_date_archive_link_type' ) ) $type = get_option( 'eg_date_archive_link_type' );
  else $type = "month";
  echo '
  <select name="eg_date_archive_link_type">
  <option ' . selected( $type, 'day', false ) . ' value="day">' . __( 'Day', 'wordpress' ).'</option>
  <option ' . selected( $type, 'month', false ) . ' value="month">' . __( 'Month', 'wordpress' ).'</option>
  <option ' . selected( $type, 'year', false ) . ' value="year">' . __( 'Year', 'wordpress' ).'</option>
  </select>
  ';
}

function get_date_archive_link(){
  if ( get_option( 'eg_date_archive_link_type' ) == "year" ){
    $res = get_year_link( get_the_date( "Y" ) );
  }
  elseif ( get_option( 'eg_date_archive_link_type' ) == "day" ){
    $res = get_day_link( get_the_date( "Y" ), get_the_date( "m" ), get_the_date( "d" ) );
  }
  else {
    $res = get_month_link( get_the_date( "Y" ), get_the_date( "m" ) );
  }
  return $res;
}

function defer_js( $tag, $handle, $src ){
  if( ! is_admin() )
    $tag = str_replace( ' src=', ' defer src=', $tag );

  return $tag;
}
# commented block below, because there may be errors with js, if need you can uncomment this block
// add_filter( 'script_loader_tag', 'defer_js', 99, 3 );

add_action('after_setup_theme', function () {
  /**
  * Enable features from Soil when plugin is activated
  * @link https://roots.io/plugins/soil/
  */
  add_theme_support('soil-clean-up');
  add_theme_support('soil-jquery-cdn');
  add_theme_support('soil-nav-walker');
  add_theme_support('soil-nice-search');
  add_theme_support('soil-relative-urls');
  /**
  * Enable plugins to manage the document title
  * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
  */
  add_theme_support('title-tag');
  /**
  * Enable post thumbnails
  * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
  */
  add_theme_support('post-thumbnails');
  /**
  * Enable HTML5 markup support
  * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
  */
  add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);
  /**
  * Enable selective refresh for widgets in customizer
  * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
  */
  // add_theme_support('customize-selective-refresh-widgets');
}, 20);

add_action('rest_api_init', function () {
  $namespace = 'presspack/v1';
  register_rest_route( $namespace, '/path/(?P<url>.*?)', array(
    'methods'  => 'GET',
    'callback' => 'get_post_for_url',
  ));
});

/**
* This fixes the wordpress rest-api so we can just lookup pages by their full
* path (not just their name). This allows us to use React Router.
*
* @return WP_Error|WP_REST_Response
*/
function get_post_for_url($data)
{
  $postId    = url_to_postid($data['url']);
  $postType  = get_post_type($postId);
  $controller = new WP_REST_Posts_Controller($postType);
  $request    = new WP_REST_Request('GET', "/wp/v2/{$postType}s/{$postId}");
  $request->set_url_params(array('id' => $postId));
  return $controller->get_item($request);
}

add_filter('body_class', 'add_slug_to_body_class'); // Add slug to body class (Starkers build)

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class($classes) {
  global $post;
  if (is_home()) {
    $key = array_search('blog', $classes);
    if ($key > -1) {
      unset($classes[$key]);
    }
  } elseif (is_page()) {
    $classes[] = sanitize_html_class($post->post_name);
  } elseif (is_singular()) {
    $classes[] = sanitize_html_class($post->post_name);
  }
  return $classes;
}
/**
 * Disable the emoji's
 */
function disable_emojis() {
 remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
 remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
 remove_action( 'wp_print_styles', 'print_emoji_styles' );
 remove_action( 'admin_print_styles', 'print_emoji_styles' );
 remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
 remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
 remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
 add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
 add_filter( 'wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2 );
}
add_action( 'init', 'disable_emojis' );

/**
 * Filter function used to remove the tinymce emoji plugin.
 *
 * @param array $plugins
 * @return array Difference betwen the two arrays
 */
function disable_emojis_tinymce( $plugins ) {
 if ( is_array( $plugins ) ) {
 return array_diff( $plugins, array( 'wpemoji' ) );
 } else {
 return array();
 }
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param array $urls URLs to print for resource hints.
 * @param string $relation_type The relation type the URLs are printed for.
 * @return array Difference betwen the two arrays.
 */
function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
 if ( 'dns-prefetch' == $relation_type ) {
 /** This filter is documented in wp-includes/formatting.php */
 $emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

$urls = array_diff( $urls, array( $emoji_svg_url ) );
 }

return $urls;
}
