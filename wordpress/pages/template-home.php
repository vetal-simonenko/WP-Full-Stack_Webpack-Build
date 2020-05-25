<?php
/*
Template Name: Home
*/
get_header(); ?>
<div id="content">
	<?php while ( have_posts( ) ) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
			<?php the_title( '<div class="title"><h1>', '</h1></div>' ); ?>
			<div class="content">
				<?php the_content(); ?>			
				<?php edit_post_link( __( 'Edit', 'base' ) ); ?>				
			</div>
		</div>
	<?php endwhile; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>