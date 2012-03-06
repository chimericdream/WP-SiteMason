<?php
get_header();
if (have_posts()) {
    while (have_posts()) {
        the_post();
        ?>
        <section <?php post_class(); ?> id="post-<?php the_ID(); ?>">
            <header>
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>
            </header>
            <article class="entry">
                <?php the_content(); ?>
            </article>
            <footer class="postmetadata">
                <?php the_tags('Tags: ', ', ', '<br />'); ?>
                Posted in <?php the_category(', '); ?> |
                <?php comments_popup_link('No Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?>
            </footer>
        </section>
        <?php
    }
    include (TEMPLATEPATH . '/inc/nav.php' );
} else {
    echo '<h2>Not Found</h2>';
}
get_footer();