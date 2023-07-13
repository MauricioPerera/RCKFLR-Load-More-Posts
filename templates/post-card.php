<?php
/**
 * The template for displaying a post card.
 *
 * @var WP_Post $post The post object.
 */

// Prevent direct file access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
?>

<div class="rckflr-post-card">
    <a href="<?php echo get_permalink($post); ?>">
        <img src="<?php echo get_the_post_thumbnail_url($post); ?>" alt="">
        <h3><?php echo get_the_title($post); ?></h3>
    </a>
</div>
