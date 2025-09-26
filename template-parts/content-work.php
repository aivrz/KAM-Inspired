<?php
/**
 * 作品内容模板部分
 */
?>

<article id="work-<?php the_ID(); ?>" <?php post_class('work-item'); ?>>
    <a href="<?php the_permalink(); ?>" class="work-link">
        <?php if (has_post_thumbnail()) : ?>
            <div class="work-image">
                <?php the_post_thumbnail('large'); ?>
            </div>
        <?php endif; ?>
        
        <div class="work-content">
            <h3 class="work-title"><?php the_title(); ?></h3>
            
            <?php if (has_excerpt()) : ?>
                <div class="work-excerpt">
                    <?php the_excerpt(); ?>
                </div>
            <?php endif; ?>
            
            <div class="work-meta">
                <span class="work-date"><?php echo get_the_date(); ?></span>
                <span class="work-category"><?php the_category(', '); ?></span>
            </div>
        </div>
    </a>
</article>