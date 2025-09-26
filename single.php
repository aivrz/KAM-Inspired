<?php get_header(); ?>

<main class="single-main">
    <div class="container">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <header class="single-header">
                <h1 class="single-title"><?php the_title(); ?></h1>
                <div class="single-meta">
                    <span class="post-date"><?php the_date(); ?></span>
                    <span class="post-category"><?php the_category(', '); ?></span>
                </div>
            </header>
            
            <?php if (has_post_thumbnail()) : ?>
                <div class="single-featured-image">
                    <?php the_post_thumbnail('large'); ?>
                </div>
            <?php endif; ?>
            
            <div class="single-content">
                <?php the_content(); ?>
            </div>
            
            <footer class="single-footer">
                <div class="post-tags">
                    <?php the_tags('', ' ', ''); ?>
                </div>
            </footer>
        </article>
        
        <div class="post-navigation">
            <?php
            previous_post_link('<div class="nav-previous">%link</div>', '← 上一篇');
            next_post_link('<div class="nav-next">%link</div>', '下一篇 →');
            ?>
        </div>

        <?php
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>
    </div>
</main>

<?php get_footer(); ?>