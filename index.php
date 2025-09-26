<?php get_header(); ?>

<div class="wechat-moments">
    <!-- 朋友圈头部 -->
    <header class="moments-header">
        <?php 
        $bg_image = get_theme_mod('moments_bg_image', get_template_directory_uri() . '/assets/images/moments-bg.jpg');
        $user_avatar = get_theme_mod('moments_user_avatar', get_template_directory_uri() . '/assets/images/default-avatar.png');
        $nickname = get_theme_mod('moments_nickname', get_bloginfo('name'));
        ?>
        <img src="<?php echo esc_url($bg_image); ?>" class="moments-bg" alt="朋友圈背景">
        
        <div class="moments-user-info">
            <div class="moments-avatar">
                <img src="<?php echo esc_url($user_avatar); ?>" alt="<?php echo esc_attr($nickname); ?>">
            </div>
            <div class="moments-user-details">
                <div class="moments-nickname"><?php echo esc_html($nickname); ?></div>
                <div class="moments-signature"><?php echo esc_html(get_theme_mod('moments_signature', '这个人很懒，什么都没有写')); ?></div>
            </div>
        </div>
    </header>

    <!-- 朋友圈内容区域 -->
    <main class="moments-content">
        <!-- 发布新动态 -->
        <section class="moment-publish">
            <textarea class="publish-input" placeholder="分享新鲜事..."></textarea>
            <div class="publish-actions">
                <div class="publish-tools">
                    <button class="publish-tool" title="添加图片">📷</button>
                    <button class="publish-tool" title="添加表情">😊</button>
                    <button class="publish-tool" title="定位">📍</button>
                </div>
                <button class="publish-submit">发布</button>
            </div>
        </section>

        <!-- 动态列表 -->
        <section class="moments-list" id="moments-list">
            <?php
            // 获取朋友圈动态（自定义文章类型）
            $moments_query = new WP_Query(array(
                'post_type' => 'moment',
                'posts_per_page' => 10,
                'meta_key' => 'moment_date',
                'orderby' => 'meta_value',
                'order' => 'DESC'
            ));
            
            if ($moments_query->have_posts()) :
                while ($moments_query->have_posts()) : $moments_query->the_post();
                    $moment_author = get_the_author();
                    $moment_avatar = get_avatar_url(get_the_author_meta('ID'), array('size' => 45));
                    $moment_time = human_time_diff(get_the_time('U'), current_time('timestamp')) . '前';
                    $moment_images = get_post_meta(get_the_ID(), 'moment_images', true);
                    $moment_likes = get_post_meta(get_the_ID(), 'moment_likes', true) ?: array();
                    $moment_comments = get_post_meta(get_the_ID(), 'moment_comments', true) ?: array();
                    ?>
                    
                    <article class="moment-item" data-moment-id="<?php the_ID(); ?>">
                        <div class="moment-header">
                            <div class="moment-avatar">
                                <img src="<?php echo esc_url($moment_avatar); ?>" alt="<?php echo esc_attr($moment_author); ?>">
                            </div>
                            <div class="moment-user-info">
                                <div class="moment-nickname"><?php echo esc_html($moment_author); ?></div>
                                <div class="moment-time"><?php echo esc_html($moment_time); ?></div>
                            </div>
                        </div>
                        
                        <div class="moment-content">
                            <?php the_content(); ?>
                        </div>
                        
                        <?php if ($moment_images) : ?>
                            <div class="moment-images">
                                <?php
                                $images = explode(',', $moment_images);
                                $image_count = count($images);
                                $grid_class = 'moment-image-grid ';
                                
                                if ($image_count === 1) {
                                    $grid_class .= 'single';
                                } elseif ($image_count === 2) {
                                    $grid_class .= 'double';
                                } elseif ($image_count === 3) {
                                    $grid_class .= 'triple';
                                } else {
                                    $grid_class .= 'multiple';
                                }
                                ?>
                                
                                <div class="<?php echo $grid_class; ?>">
                                    <?php foreach (array_slice($images, 0, 9) as $image_url) : ?>
                                        <div class="moment-image">
                                            <img src="<?php echo esc_url($image_url); ?>" 
                                                 alt="动态图片" 
                                                 data-src="<?php echo esc_url($image_url); ?>">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="moment-actions">
                            <div class="moment-stats">
                                <?php if (!empty($moment_likes)) : ?>
                                    <span class="like-count"><?php echo count($moment_likes); ?> 赞</span>
                                <?php endif; ?>
                                <?php if (!empty($moment_comments)) : ?>
                                    <span class="comment-count"><?php echo count($moment_comments); ?> 评论</span>
                                <?php endif; ?>
                            </div>
                            <div class="moment-interaction">
                                <button class="moment-like" 
                                        data-moment-id="<?php the_ID(); ?>"
                                        data-liked="<?php echo in_array(get_current_user_id(), $moment_likes) ? 'true' : 'false'; ?>">
                                    👍 赞
                                </button>
                                <button class="moment-comment" data-moment-id="<?php the_ID(); ?>">
                                    💬 评论
                                </button>
                            </div>
                        </div>
                        
                        <!-- 点赞列表 -->
                        <?php if (!empty($moment_likes)) : ?>
                            <div class="moment-likes">
                                👍 
                                <?php
                                $like_names = array();
                                foreach ($moment_likes as $user_id) {
                                    $user = get_userdata($user_id);
                                    if ($user) {
                                        $like_names[] = $user->display_name;
                                    }
                                }
                                echo implode(', ', $like_names);
                                ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 评论列表 -->
                        <?php if (!empty($moment_comments)) : ?>
                            <div class="moment-comments">
                                <?php foreach ($moment_comments as $comment) : ?>
                                    <div class="moment-comment-item">
                                        <span class="moment-comment-author"><?php echo esc_html($comment['author']); ?>：</span>
                                        <span class="moment-comment-text"><?php echo esc_html($comment['content']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- 评论输入框 -->
                        <div class="moment-comment-input" style="display: none;">
                            <input type="text" placeholder="评论..." class="comment-text">
                            <button class="comment-submit">发送</button>
                        </div>
                    </article>
                    
                <?php endwhile;
            else : ?>
                <div class="no-moments">
                    <p>还没有动态，快来发布第一条吧！</p>
                </div>
            <?php endif; ?>
        </section>
        
        <!-- 加载更多 -->
        <div class="load-more">
            <button class="load-more-button" id="load-more-moments">加载更多</button>
        </div>
    </main>
</div>

<!-- 图片预览模态框 -->
<div class="image-modal" id="image-modal">
    <button class="modal-close">&times;</button>
    <img class="modal-image" src="" alt="预览图片">
</div>

<?php get_footer(); ?>