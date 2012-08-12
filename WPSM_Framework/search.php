<?php
get_header();
if (have_posts()) {
    ?>
    <h2>Search Results</h2>
    <?php
    include (TEMPLATEPATH . '/inc/nav.php' );
    while (have_posts()) {
        the_post();
        ?>
        <section <?php post_class(); ?> id="post-<?php the_ID(); ?>">
            <header>
                <h2><?php the_title(); ?></h2>
                <?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>
            </header>
            <article class="entry">
                <?php the_excerpt(); ?>
            </article>
        </section>
        <?php
    }
    include (TEMPLATEPATH . '/inc/nav.php' );
} else {
    echo '<h2>No posts found.</h2>';
}
get_sidebar();
get_footer();
