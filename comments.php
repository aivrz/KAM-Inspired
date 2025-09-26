<?php
if (post_password_required()) {
    return;
}
?>

<div id="comments" class="comments-area">
    <?php if (have_comments()) : ?>
        <h2 class="comments-title">
            <?php
            $comments_number = get_comments_number();
            if ($comments_number === 1) {
                printf(__('一条留言'), get_the_title());
            } else {
                printf(
                    _n('%s条留言', '%s条留言', $comments_number),
                    number_format_i18n($comments_number)
                );
            }
            ?>
        </h2>
        
        <ol class="comment-list">
            <?php
            wp_list_comments(array(
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 0,
                'callback' => 'kam_custom_comment_list'
            ));
            ?>
        </ol>
        
        <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
            <nav class="comment-navigation">
                <?php paginate_comments_links(); ?>
            </nav>
        <?php endif; ?>
        
    <?php endif; ?>
    
    <?php if (!comments_open() && get_comments_number() && post_type_supports(get_post_type(), 'comments')) : ?>
        <p class="no-comments"><?php _e('评论已关闭'); ?></p>
    <?php endif; ?>
    
    <?php
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = $req ? " aria-required='true'" : '';
    
    $fields = array(
        'author' => '
            <div class="comment-form-author">
                <label for="author">' . __('姓名') . ($req ? ' <span class="required">*</span>' : '') . '</label>
                <input id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" size="30"' . $aria_req . ' />
            </div>',
        
        'cookies' => '
            <div class="comment-form-cookies-consent">
                <input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" />
                <label for="wp-comment-cookies-consent">' . __('保存我的姓名、邮箱和网站信息，以便下次评论时使用。') . '</label>
            </div>'
    );
    
    $args = array(
        'title_reply' => __('发表留言'),
        'title_reply_to' => __('回复给 %s'),
        'cancel_reply_link' => __('取消回复'),
        'label_submit' => __('提交留言'),
        
        'comment_field' => '
            <div class="comment-form-comment">
                <label for="comment">' . _x('留言内容', 'noun') . '</label>
                <textarea id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
            </div>',
        
        'fields' => apply_filters('comment_form_default_fields', $fields),
        
        'comment_notes_before' => '
            <p class="comment-notes">' . __('您的邮箱地址不会被公开。必填项已用*标注') . '</p>',
            
        'class_submit' => 'submit-btn'
    );
    
    comment_form($args);
    ?>
</div>