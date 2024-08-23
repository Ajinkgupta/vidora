<?php
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <header class="page-header">
            <h1 class="page-title"><?php _e('Videos', 'doubtly-vidora'); ?></h1>
        </header>

        <?php if (have_posts()) : ?>
            <div class="video-grid">
                <?php while (have_posts()) : the_post(); ?>
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
            </div>

            <?php the_posts_navigation(); ?>

        <?php else : ?>
            <p><?php _e('No videos found.', 'doubtly-vidora'); ?></p>
        <?php endif; ?>

    </main>
</div>

<?php
get_sidebar();
get_footer();
?>
