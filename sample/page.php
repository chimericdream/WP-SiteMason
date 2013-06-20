<?php
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <section class="post" id="post-<?php the_ID(); ?>">
            <header>
                <h2><?php the_title(); ?></h2>
                <?php include (THEME_PATH . '/inc/meta.php' ); ?>
            </header>
            <article class="entry">
                <?php the_content(); ?>
                <?php wp_link_pages(array('before' => 'Pages: ', 'next_or_number' => 'number')); ?>
            </article>
            <footer>
                <?php edit_post_link('Edit this entry.', '<p>', '</p>'); ?>
            </footer>
        </section>
        <?php
        comments_template();
    }
}
get_footer();