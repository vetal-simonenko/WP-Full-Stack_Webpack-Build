<?php
add_action('wp_enqueue_scripts', function () {
  if (file_exists(get_template_directory() . '/build/assets.json')) {
    $manifest = json_decode(file_get_contents(get_template_directory() . '/build/assets.json', true));
    $main = $manifest->main;
    if (isset($main->js)) {
      wp_enqueue_script('theme-js', get_template_directory_uri() . "/build/" . $main->js, ['jquery'], null, true);
    }
  }
  wp_enqueue_style('style', get_stylesheet_uri());
}, 100);


add_action('admin_head', function() {
  echo '<style>
  .wp-block{
    max-width: 1340px;
  }
  </style>';
});


add_action('wp_enqueue_scripts', function() {
  if (file_exists(get_template_directory() . '/build/assets.json')) {
    $manifest = json_decode(file_get_contents(get_template_directory() . '/build/assets.json', true));
    $main = $manifest->main;
    if (isset($main->css)) {
      $main_style_uri = '/build/' . $main->css;
      $file_path = get_template_directory() . $main_style_uri;
      $theme     = wp_get_theme();
      $theme_ver = $theme->version;
      $file_path = get_template_directory_uri() . $main_style_uri;
      printf("<link rel=\"stylesheet\" href=\"%s\" id=\"thestyle\">", "$file_path?ver=$theme_ver");
    }
  }
}, 100);

