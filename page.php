<?php get_header(); ?>

<main class="page-main">
    <div class="container">
        <div class="page-header">
            <h1 class="page-title"><?php the_title(); ?></h1>
            <?php if (get_the_excerpt()) : ?>
                <p class="page-description"><?php echo get_the_excerpt(); ?></p>
            <?php endif; ?>
        </div>
        
        <div class="page-content">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
            <?php endwhile; endif; ?>
        </div>
    </div>
</main>

<?php get_footer(); ?>