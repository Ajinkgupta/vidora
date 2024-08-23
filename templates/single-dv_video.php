<?php
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php while (have_posts()) : the_post(); ?>

        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
            </header>

            <div class="entry-content">
                <?php
                $youtube_url = get_post_meta(get_the_ID(), '_dv_youtube_url', true);
                if ($youtube_url) {
                    echo wp_oembed_get($youtube_url);
                }
                ?>

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
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    <?php endwhile; ?>

    </main>
</div>

<?php
get_sidebar();
get_footer();
?>
