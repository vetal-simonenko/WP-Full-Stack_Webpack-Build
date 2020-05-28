<?php
add_action('acf/init', 'my_acf_init');
function my_acf_init() {
  if( function_exists('acf_register_block') ) {
    if ($block_fiels = scandir( get_theme_file_path('/blocks/acf/') )) {
      $block_fiels = array_diff( $block_fiels, ['.', '..']);
      $block_fiels = array_filter( $block_fiels, function($file){ if(!is_dir($file)) return $file;  } );
      foreach ($block_fiels as $file_name) {
        $block_name = str_replace('.php', '', $file_name);
        $file_path = get_theme_file_path('/blocks/acf/'.$file_name);
        $file_data = get_file_data($file_path, [ 'title'=>'Block title','description'=>'Description','keywords'=>'Keywords','category'=>'Category','icon'=>'Icon' ]);
        $file_data['keywords'] = explode(', ', $file_data['keywords']);
        $file_data['keywords'] = array_map('trim', $file_data['keywords']);
        //fallback options
        $file_data['title'] = $file_data['title'] ? $file_data['title'] : $block_name;
        $file_data['description'] = $file_data['description'] ? $file_data['description'] : $block_name;
        $file_data['category'] = $file_data['category'] ? $file_data['category'] : 'theme-blocks';
        $file_data['icon'] = $file_data['icon'] ? $file_data['icon'] : 'media-text';
        acf_register_block([
          'name'             => $block_name,
          'title'            => $file_data['title'],
          'description'      => $file_data['description'],
          'render_template'  => $file_path,
          'category'         => $file_data['category'],
          'icon'             => $file_data['icon'],
          'keywords'         => $file_data['keywords'],
          'mode'             => 'edit',
          'align'            => 'full',
          'supports'         => [ 'align' => false, 'mode' => false, ]
        ]);
      }
    }
  }
}

add_filter( 'block_categories', 'my_plugin_block_categories', 10, 2 );
function my_plugin_block_categories( $categories, $post ) {
  return array_merge($categories,[['slug'   => 'theme-blocks', 'title'  => __( 'Theme blocks', 'base' ) ]]);
}

function my_acf_block_render_callback( $block ) {
  $slug = str_replace('acf/', '', $block['name']);
  if( file_exists( get_theme_file_path("/blocks/acf/{$slug}.php") ) ) {
    include( get_theme_file_path("/blocks/acf/{$slug}.php") );
  }
}

function theme_gutenberg_default_block_wrapper( $block_content, $block ) {
  $core_blocks = [
    'core/shortcode',
    'core/image',
    'core/gallery',
    'core/heading',
    'core/quote',
    'core/embed',
    'core/list',
    'core/separator',
    'core/more',
    'core/button',
    'core/pullquote',
    'core/table',
    //'core/preformatted',
    //'core/code',
    //'core/html',
    'core/freeform',
    'core/latest-posts',
    'core/categories',
    'core/cover',
    'core/text-columns',
    'core/verse',
    'core/video',
    'core/audio',
    'core/block',
    'core/paragraph',
    'core-embed/twitter',
    'core-embed/youtube',
    'core-embed/facebook',
    'core-embed/instagram',
    'core-embed/wordpress',
    'core-embed/soundcloud',
    'core-embed/spotify',
    'core-embed/flickr',
    'core-embed/vimeo',
    'core-embed/animoto',
    'core-embed/cloudup',
    'core-embed/collegehumor',
    'core-embed/dailymotion',
    'core-embed/funnyordie',
    'core-embed/hulu',
    'core-embed/imgur',
    'core-embed/issuu',
    'core-embed/kickstarter',
    'core-embed/meetup-com',
    'core-embed/mixcloud',
    'core-embed/photobucket',
    'core-embed/polldaddy',
    'core-embed/reddit',
    'core-embed/reverbnation',
    'core-embed/screencast',
    'core-embed/scribd',
    'core-embed/slideshare',
    'core-embed/smugmug',
    'core-embed/speaker',
    'core-embed/ted',
    'core-embed/tumblr',
    'core-embed/videopress',
    'core-embed/wordpress-tv',
  ];
  if (in_array($block['blockName'], $core_blocks) && ($block['blockName'] != 'core/block' && !isset($block['attrs']['ref']))) {
    if ( preg_replace("/\s/", "", $block_content) != '') {
      $block_content = '<div class="base-section"><div class="container">'.$block_content.'</div></div>';
    }
  }
  return $block_content;
}
add_filter( 'render_block', 'theme_gutenberg_default_block_wrapper', 10, 2 );
