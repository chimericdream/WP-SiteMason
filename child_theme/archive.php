<?php
get_header();
if (have_posts()) {
    $post = $posts[0]; // Hack. Set $post so that the_date() works.
    if (is_category()) { /* If this is a category archive */
        echo '    <h2>Archive for the &#8216;';
        single_cat_title();
        echo '&#8217; Category</h2>';
    } elseif(is_tag()) { /* If this is a tag archive */
        echo '    <h2>Posts Tagged &#8216;';
        single_tag_title();
        echo '&#8217;</h2>';
    } elseif (is_day()) { /* If this is a daily archive */
        echo '    <h2>Archive for ';
        the_time('F jS, Y');
        echo '</h2>';
    } elseif (is_month()) { /* If this is a monthly archive */
        echo '    <h2>Archive for ';
        the_time('F, Y');
        echo '</h2>';
    } elseif (is_year()) { /* If this is a yearly archive */
        echo '    <h2 class="pagetitle">Archive for ';
        the_time('Y');
        echo '</h2>';
    } elseif (is_author()) { /* If this is an author archive */
        echo '    <h2 class="pagetitle">Author Archive</h2>';
    } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { /* If this is a paged archive */
        echo '    <h2 class="pagetitle">Blog Archives</h2>';
    }
    include (TEMPLATEPATH . '/inc/nav.php' );
    while (have_posts()) {
        the_post();
        ?>
        <section <?php post_class(); ?>>
            <header>
                <h2 id="post-<?php the_ID(); ?>"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php include (TEMPLATEPATH . '/inc/meta.php' ); ?>
            </header>
            <article class="entry">
                <?php the_content(); ?>
            </article>
        </section>
        <?php
    }
    include (TEMPLATEPATH . '/inc/nav.php' );
} else {
    echo '<h2>Nothing found</h2>';
}
get_footer();