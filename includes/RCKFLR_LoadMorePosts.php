<?php
use Carbon_Fields\Block;
use Carbon_Fields\Field;

class RCKFLR_LoadMorePosts {
    private $initial_posts_count = 2;
    private $load_more_posts_count = 2;

    public function __construct() {
        add_action('wp_ajax_rckflr_load_more', array($this, 'ajax_load_more'));
        add_action('wp_ajax_nopriv_rckflr_load_more', array($this, 'ajax_load_more'));
        add_action('wp_head', array($this, 'add_inline_css'));
        add_action('wp_footer', array($this, 'add_inline_js'));
    }

    public function register_block() {
        Block::make('Load More Posts')
            ->add_fields(array(
                Field::make('text', 'post_type', 'Post Type')
                    ->set_default_value('post'),
                Field::make('text', 'taxonomy', 'Taxonomy')
                    ->set_default_value('category'),
                Field::make('text', 'term', 'Term')
                    ->set_default_value(''),
                Field::make('number', 'initial_posts', 'Initial Posts')
                    ->set_default_value($this->initial_posts_count),
                Field::make('number', 'load_more_posts', 'Load More Posts')
                    ->set_default_value($this->load_more_posts_count),
            ))
            ->set_render_callback(array($this, 'render_block'));
    }

    public function render_block($block) {
        $post_type = $block['post_type'];
        $taxonomy = $block['taxonomy'];
        $term = $block['term'];
        $initial_posts = $block['initial_posts'];
        $load_more_posts = $block['load_more_posts'];

        // ...
    }

    private function get_posts($post_type, $taxonomy, $term, $count, $offset = 0) {
        $args = array(
            'post_type' => $post_type,
            'tax_query' => array(
                array(
                    'taxonomy' => $taxonomy,
                    'field'    => 'slug',
                    'terms'    => $term,
                ),
            ),
            'posts_per_page' => $count,
            'offset' => $offset,
        );
        return get_posts($args);
    }

    // ...
}
