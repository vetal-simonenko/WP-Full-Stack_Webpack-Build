<!DOCTYPE html>
<html <?php language_attributes() ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ) ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ) ?>">
  <script type="text/javascript">
    var pathInfo = {
      base: '<?php echo get_template_directory_uri() ?>/',
      css: 'css/',
      js: 'js/',
      swf: 'swf/',
    }
  </script>
  <?php wp_head() ?>
</head>
<body <?php body_class() ?>>
  <div id="wrapper">
    <div class="wrapper-inner">
      <?php if($logo = get_field('logo', 'options')): ?>
        <strong class="logo" itemscope itemtype="http://schema.org/Brand">
         <a href="<?php echo home_url() ?>">
           <?php echo wp_get_attachment_image($logo, 'full') ?>
         </a>
       </strong>
     <?php endif ?>
     <?php if( has_nav_menu( 'primary' ) )
     wp_nav_menu( array(
      'container' => false,
      'theme_location' => 'primary',
      'menu_id'        => 'navigation',
      'menu_class'     => 'navigation',
      'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
      'walker'         => new Custom_Walker_Nav_Menu
    )); ?>
    <main role="main" aria-label="Content">
