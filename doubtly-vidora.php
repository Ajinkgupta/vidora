<?php
/**
 * Plugin Name: Doubtly Vidora
 * Plugin URI: https://github.com/ajinkgupta/doubtly-vidora
 * Description: A WordPress plugin for managing video content with custom post type and widget.
 * Version: 1.0.0
 * Author: Ajink Gupta
 * Author URI: https://github.com/ajinkgupta
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Register Custom Post Type
function doubtly_vidora_register_cpt() {
    $labels = array(
        'name'                  => _x('Videos', 'Post Type General Name', 'doubtly-vidora'),
        'singular_name'         => _x('Video', 'Post Type Singular Name', 'doubtly-vidora'),
        'menu_name'             => __('Videos', 'doubtly-vidora'),
        'name_admin_bar'        => __('Video', 'doubtly-vidora'),
        'archives'              => __('Video Archives', 'doubtly-vidora'),
        'attributes'            => __('Video Attributes', 'doubtly-vidora'),
        'parent_item_colon'     => __('Parent Video:', 'doubtly-vidora'),
        'all_items'             => __('All Videos', 'doubtly-vidora'),
        'add_new_item'          => __('Add New Video', 'doubtly-vidora'),
        'add_new'               => __('Add New', 'doubtly-vidora'),
        'new_item'              => __('New Video', 'doubtly-vidora'),
        'edit_item'             => __('Edit Video', 'doubtly-vidora'),
        'update_item'           => __('Update Video', 'doubtly-vidora'),
        'view_item'             => __('View Video', 'doubtly-vidora'),
        'view_items'            => __('View Videos', 'doubtly-vidora'),
        'search_items'          => __('Search Video', 'doubtly-vidora'),
        'not_found'             => __('Not found', 'doubtly-vidora'),
        'not_found_in_trash'    => __('Not found in Trash', 'doubtly-vidora'),
        'featured_image'        => __('Featured Image', 'doubtly-vidora'),
        'set_featured_image'    => __('Set featured image', 'doubtly-vidora'),
        'remove_featured_image' => __('Remove featured image', 'doubtly-vidora'),
        'use_featured_image'    => __('Use as featured image', 'doubtly-vidora'),
        'insert_into_item'      => __('Insert into video', 'doubtly-vidora'),
        'uploaded_to_this_item' => __('Uploaded to this video', 'doubtly-vidora'),
        'items_list'            => __('Videos list', 'doubtly-vidora'),
        'items_list_navigation' => __('Videos list navigation', 'doubtly-vidora'),
        'filter_items_list'     => __('Filter videos list', 'doubtly-vidora'),
    );
    $args = array(
        'label'                 => __('Video', 'doubtly-vidora'),
        'description'           => __('Video content type', 'doubtly-vidora'),
        'labels'                => $labels,
        'supports'              => array('title', 'editor', 'thumbnail', 'comments', 'revisions', 'custom-fields'),
        'taxonomies'            => array('category', 'post_tag'),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-video-alt3',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => 'videos',
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array('slug' => 'video'),
    );
    register_post_type('dv_video', $args);
}
add_action('init', 'doubtly_vidora_register_cpt', 0);

// Add custom meta box for YouTube URL
function doubtly_vidora_add_meta_box() {
    add_meta_box(
        'dv_youtube_url',
        __('YouTube Video URL', 'doubtly-vidora'),
        'doubtly_vidora_youtube_url_callback',
        'dv_video',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'doubtly_vidora_add_meta_box');

// Meta box callback function
function doubtly_vidora_youtube_url_callback($post) {
    wp_nonce_field('doubtly_vidora_save_meta_box_data', 'doubtly_vidora_meta_box_nonce');
    $value = get_post_meta($post->ID, '_dv_youtube_url', true);
    echo '<label for="dv_youtube_url">';
    _e('YouTube Video URL:', 'doubtly-vidora');
    echo '</label> ';
    echo '<input type="text" id="dv_youtube_url" name="dv_youtube_url" value="' . esc_attr($value) . '" size="50" />';
}

// Save meta box data
function doubtly_vidora_save_meta_box_data($post_id) {
    if (!isset($_POST['doubtly_vidora_meta_box_nonce'])) {
        return;
    }
    if (!wp_verify_nonce($_POST['doubtly_vidora_meta_box_nonce'], 'doubtly_vidora_save_meta_box_data')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (!isset($_POST['dv_youtube_url'])) {
        return;
    }
    $youtube_url = sanitize_text_field($_POST['dv_youtube_url']);
    update_post_meta($post_id, '_dv_youtube_url', $youtube_url);
}
add_action('save_post', 'doubtly_vidora_save_meta_box_data');

// Register widget
function doubtly_vidora_register_widget() {
    register_widget('Doubtly_Vidora_Similar_Videos_Widget');
}
add_action('widgets_init', 'doubtly_vidora_register_widget');

// Similar Videos Widget
class Doubtly_Vidora_Similar_Videos_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'doubtly_vidora_similar_videos',
            __('Similar Videos', 'doubtly-vidora'),
            array('description' => __('Displays similar videos on single video pages', 'doubtly-vidora'))
        );
    }

    public function widget($args, $instance) {
        if (!is_singular('dv_video')) {
            return;
        }

        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : __('Similar Videos', 'doubtly-vidora');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;

        echo $args['before_widget'];
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $current_post_id = get_the_ID();
        $current_post_terms = wp_get_post_terms($current_post_id, 'category', array('fields' => 'ids'));

        $similar_videos = new WP_Query(array(
            'post_type' => 'dv_video',
            'posts_per_page' => $number,
            'post__not_in' => array($current_post_id),
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field' => 'id',
                    'terms' => $current_post_terms
                )
            )
        ));

        if ($similar_videos->have_posts()) {
            echo '<ul>';
            while ($similar_videos->have_posts()) {
                $similar_videos->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
        } else {
            echo __('No similar videos found.', 'doubtly-vidora');
        }

        wp_reset_postdata();

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Similar Videos', 'doubtly-vidora');
        $number = !empty($instance['number']) ? absint($instance['number']) : 3;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_attr_e('Title:', 'doubtly-vidora'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('number')); ?>"><?php esc_attr_e('Number of videos to show:', 'doubtly-vidora'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('number')); ?>" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($number); ?>" size="3">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? absint($new_instance['number']) : 3;
        return $instance;
    }
}

// Enqueue scripts and styles
function doubtly_vidora_enqueue_scripts() {
    wp_enqueue_style('doubtly-vidora-style', plugin_dir_url(__FILE__) . 'css/doubtly-vidora.css');
    wp_enqueue_script('doubtly-vidora-script', plugin_dir_url(__FILE__) . 'js/doubtly-vidora.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'doubtly_vidora_enqueue_scripts');

// Load templates from plugin directory
function doubtly_vidora_load_templates($template) {
    if (is_singular('dv_video')) {
        $template = plugin_dir_path(__FILE__) . 'templates/single-dv_video.php';
    } elseif (is_post_type_archive('dv_video')) {
        $template = plugin_dir_path(__FILE__) . 'templates/archive-dv_video.php';
    }
    return $template;
}
add_filter('template_include', 'doubtly_vidora_load_templates');
