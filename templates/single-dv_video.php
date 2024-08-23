<?php
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php while (have_posts()) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

            <div class="entry-content">
                <?php
                $youtube_url = get_post_meta(get_the_ID(), '_dv_youtube_url', true);
                if ($youtube_url) {
                    echo wp_oembed_get($youtube_url);
                }
                ?>
				
                <header class="entry-header">
                    <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                </header>

                <?php the_content(); ?>
            </div>

            <footer class="entry-footer">
                <?php
                echo '<div class="entry-meta">';
                echo __('Posted by ', 'doubtly-vidora') . get_the_author() . ' | ' . get_the_date();
                echo '</div>';

                $categories_list = get_the_category_list(', ');
                if ($categories_list) {
                    echo '<span class="cat-links">' . __('Categories: ', 'doubtly-vidora') . $categories_list . '</span>';
                }

                $tags_list = get_the_tag_list('', ', ');
                if ($tags_list) {
                    echo '<span class="tags-links">' . __('Tags: ', 'doubtly-vidora') . $tags_list . '</span>';
                }
                ?>
            </footer>
        </article>

        <?php
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    <?php endwhile; ?>

    </main>
</div>

<!-- Related Videos Section -->
<div id="secondary" class="widget-area">
    <?php
    // Get categories and tags of the current post
    $categories = wp_get_post_categories(get_the_ID());
    $tags = wp_get_post_tags(get_the_ID());

    // Query related videos by categories and tags
    $related_query_args = array(
        'post_type' => 'dv_video',
        'posts_per_page' => 5,
        'post__not_in' => array(get_the_ID()), // Exclude the current post
        'category__in' => $categories,
        'tag__in' => wp_list_pluck($tags, 'term_id'), // Tags in current post
        'orderby' => 'date',
        'order' => 'DESC',
    );
    $related_query = new WP_Query($related_query_args);

    if ($related_query->have_posts()) : ?>
        <div class="video-grid">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('video-item'); ?>>
                    <a href="<?php the_permalink(); ?>">
                        <?php the_post_thumbnail('medium'); ?>
                        <h2 class="entry-title"><?php the_title(); ?></h2>
                    </a>
                    <div class="entry-meta">
                        <?php echo get_the_date(); ?> | <?php the_author(); ?>
                    </div>
                </article>
            <?php endwhile; ?>
            <?php wp_reset_postdata(); ?>
        </div>

    <?php else :
        // Fallback to random videos if no related videos found
        $random_query_args = array(
            'post_type' => 'dv_video',
            'posts_per_page' => 5,
            'post__not_in' => array(get_the_ID()), // Exclude the current post
            'orderby' => 'rand',
        );
        $random_query = new WP_Query($random_query_args);

        if ($random_query->have_posts()) : ?>
            <div class="video-grid">
                <?php while ($random_query->have_posts()) : $random_query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('video-item'); ?>>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_post_thumbnail('medium'); ?>
                            <h2 class="entry-title"><?php the_title(); ?></h2>
                        </a>
                        <div class="entry-meta">
                            <?php echo get_the_date(); ?> | <?php the_author(); ?>
                        </div>
                    </article>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            </div>

        <?php else : ?>
            <p><?php _e('No videos found.', 'doubtly-vidora'); ?></p>
        <?php endif;
    endif;
    ?>
</div>

<?php
get_footer();
?>
