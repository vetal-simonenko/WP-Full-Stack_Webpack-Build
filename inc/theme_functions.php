<?php
// Theme functions
/**
 * Check if WooCommerce is active
 **/
add_action('after_setup_theme', 'woocommerce_support_theme');
function woocommerce_support_theme()
{
  if (
    in_array(
      'woocommerce/woocommerce.php',
      apply_filters('active_plugins', get_option('active_plugins'))
    )
  ) {
    add_theme_support('woocommerce');
    // Replace product single page
    function woocommerce_content()
    {

      if (is_singular('product')) {

        while (have_posts()) :
          the_post();
          wc_get_template_part('content', 'single-product');
        endwhile;
      } elseif (is_shop()) {
        wc_get_template_part('archive', 'product');
      } else {
?>

        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>

          <h1 class="page-title"><?php woocommerce_page_title(); ?></h1>

        <?php endif; ?>

        <?php do_action('woocommerce_archive_description'); ?>

        <?php if (woocommerce_product_loop()) : ?>

          <?php do_action('woocommerce_before_shop_loop'); ?>

          <?php woocommerce_product_loop_start(); ?>

          <?php if (wc_get_loop_prop('total')) : ?>
            <?php while (have_posts()) : ?>
              <?php the_post(); ?>
              <?php wc_get_template_part('content', 'product'); ?>
            <?php endwhile; ?>
          <?php endif; ?>

          <?php woocommerce_product_loop_end(); ?>

          <?php do_action('woocommerce_after_shop_loop'); ?>

<?php
        else :
          do_action('woocommerce_no_products_found');
        endif;
      }
    }
  }
}
