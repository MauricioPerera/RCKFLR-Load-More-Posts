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

    $initial_posts = $this->get_posts($post_type, $taxonomy, $term, $initial_posts);
    $output = '<div id="rckflr-posts-grid">';

    foreach ($initial_posts as $post) {
        $output .= $this->post_card($post);
    }

    $output .= '</div><button id="rckflr-load-more" data-offset="' . $initial_posts . '" data-loadmore="' . $load_more_posts . '">' . __('Load More') . '</button>';
    echo $output;
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

    public function ajax_load_more() {
        $offset = isset($_POST['offset']) ? intval($_POST['offset']) : 0;
        $load_more_posts = isset($_POST['load_more_posts']) ? intval($_POST['load_more_posts']) : $this->load_more_posts_count;
        
        $posts = $this->get_posts($load_more_posts, $offset);
        foreach ($posts as $post) {
            echo $this->post_card($post);
        }
        wp_die();
    }

    public function add_inline_css() {
        echo '<style>
            .rckflr-post-card {
                display: inline-block;
                width: 200px;
                border: 1px solid #ccc;
                padding: 15px;
                margin: 10px;
            }
            .rckflr-post-card img {
                width: 100%;
            }
            .rckflr-post-card h3 {
                font-size: 18px;
                margin: 10px 0;
            }
        </style>';
    }

    public function add_inline_js() {
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                const loadMoreBtn = document.getElementById('rckflr-load-more');

                loadMoreBtn.addEventListener('click', function() {
                    const offset = parseInt(this.getAttribute('data-offset'));
                    const loadMorePosts = parseInt(this.getAttribute('data-loadmore'));
                    const xhr = new XMLHttpRequest();

                    xhr.open('POST', '" . admin_url('admin-ajax.php') . "', true);
                    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    xhr.onreadystatechange = function() {
                        if (this.readyState === 4 && this.status === 200) {
                            document.getElementById('rckflr-posts-grid').insertAdjacentHTML('beforeend', this.responseText);
                            loadMoreBtn.setAttribute('data-offset', offset + loadMorePosts);
                        }
                    };

                    xhr.send('action=rckflr_load_more&offset=' + offset + '&load_more_posts=' + loadMorePosts);
                });
            });
        </script>";
    }
}

new RCKFLR_LoadMorePosts();
