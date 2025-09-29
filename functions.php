<?php
// 主题设置
function kam_theme_setup() {
    // 支持特色图像
    add_theme_support('post-thumbnails');
    
    // 支持菜单
    register_nav_menus(array(
        'primary' => '主导航菜单'
    ));
    
    // 支持标题标签
    add_theme_support('title-tag');
    
    // 支持HTML5
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption'
    ));
    
    // 自定义Logo支持
    add_theme_support('custom-logo', array(
        'height' => 50,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
}
add_action('after_setup_theme', 'kam_theme_setup');

// 注册样式和脚本
function kam_theme_scripts() {
    wp_enqueue_style('main-style', get_stylesheet_uri());
    wp_enqueue_script('main-js', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true);
    
    // 为AJAX传递参数
    wp_localize_script('main-js', 'ajaxurl', admin_url('admin-ajax.php'));
    // 传递AJAX地址和nonce
    wp_localize_script('main-js', 'ajaxParams', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'commentNonce' => wp_create_nonce('moment_comment_nonce') // 生成nonce
    ));
    // 传递用户登录状态
    wp_localize_script('main-js', 'kamUser', array(
        'loggedIn' => is_user_logged_in()
    ));
}
add_action('wp_enqueue_scripts', 'kam_theme_scripts');

// 自定义作品集文章类型
function kam_create_works_post_type() {
    register_post_type('works',
        array(
            'labels' => array(
                'name' => __('作品集'),
                'singular_name' => __('作品')
            ),
            'public' => true,
            'has_archive' => true,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
            'menu_icon' => 'dashicons-portfolio'
        )
    );
}
add_action('init', 'kam_create_works_post_type');

// 添加朋友圈自定义文章类型
function kam_create_moment_post_type() {
    register_post_type('moment',
        array(
            'labels' => array(
                'name' => __('朋友圈动态'),
                'singular_name' => __('动态')
            ),
            'public' => true,
            'has_archive' => false,
            'supports' => array('title', 'editor', 'author'),
            'menu_icon' => 'dashicons-format-status',
            'show_in_rest' => true
        )
    );
}
add_action('init', 'kam_create_moment_post_type');

// 添加小工具支持
function kam_widgets_init() {
    register_sidebar(array(
        'name' => '侧边栏',
        'id' => 'sidebar-1',
        'description' => '主要侧边栏',
        'before_widget' => '<div class="widget">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>'
    ));
}
add_action('widgets_init', 'kam_widgets_init');

/**
 * 主题自定义设置
 */
function kam_customize_register($wp_customize) {
    
    // === 基本设置部分 ===
    $wp_customize->add_section('kam_general_settings', array(
        'title' => __('基本设置', 'kam-inspired'),
        'priority' => 30,
    ));
    
    // 网站Logo
    $wp_customize->add_setting('kam_logo', array(
        'default' => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'kam_logo', array(
        'label' => __('网站Logo', 'kam-inspired'),
        'section' => 'kam_general_settings',
        'settings' => 'kam_logo',
    )));
    
    // 网站描述
    $wp_customize->add_setting('kam_site_description', array(
        'default' => __('简约而不简单的设计解决方案', 'kam-inspired'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('kam_site_description', array(
        'label' => __('网站描述', 'kam-inspired'),
        'section' => 'kam_general_settings',
        'type' => 'text',
    ));
    
    // === 颜色设置部分 ===
    $wp_customize->add_section('kam_color_settings', array(
        'title' => __('颜色设置', 'kam-inspired'),
        'priority' => 40,
    ));
    
    // 主色调
    $wp_customize->add_setting('kam_primary_color', array(
        'default' => '#ff3e7f',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kam_primary_color', array(
        'label' => __('主色调', 'kam-inspired'),
        'section' => 'kam_color_settings',
        'settings' => 'kam_primary_color',
    )));
    
    // 背景色
    $wp_customize->add_setting('kam_bg_color', array(
        'default' => '#0a0a0a',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kam_bg_color', array(
        'label' => __('背景颜色', 'kam-inspired'),
        'section' => 'kam_color_settings',
        'settings' => 'kam_bg_color',
    )));
    
    // 文字颜色
    $wp_customize->add_setting('kam_text_color', array(
        'default' => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ));
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'kam_text_color', array(
        'label' => __('文字颜色', 'kam-inspired'),
        'section' => 'kam_color_settings',
        'settings' => 'kam_text_color',
    )));
    
    // === 朋友圈设置部分 ===
    $wp_customize->add_section('kam_moments_settings', array(
        'title' => __('朋友圈设置', 'kam-inspired'),
        'priority' => 80,
    ));
    
    // 用户头像
    $wp_customize->add_setting('moments_user_avatar', array(
        'default' => get_template_directory_uri() . '/assets/images/default-avatar.png',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'moments_user_avatar', array(
        'label' => __('用户头像', 'kam-inspired'),
        'section' => 'kam_moments_settings',
        'settings' => 'moments_user_avatar',
    )));
    
    // 背景图片
    $wp_customize->add_setting('moments_bg_image', array(
        'default' => get_template_directory_uri() . '/assets/images/moments-bg.jpg',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'moments_bg_image', array(
        'label' => __('朋友圈背景', 'kam-inspired'),
        'section' => 'kam_moments_settings',
        'settings' => 'moments_bg_image',
    )));
    
    // 用户昵称
    $wp_customize->add_setting('moments_nickname', array(
        'default' => get_bloginfo('name'),
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('moments_nickname', array(
        'label' => __('用户昵称', 'kam-inspired'),
        'section' => 'kam_moments_settings',
        'type' => 'text',
    ));
    
    // 个性签名
    $wp_customize->add_setting('moments_signature', array(
        'default' => '这个人很懒，什么都没有写',
        'sanitize_callback' => 'sanitize_text_field',
    ));
    
    $wp_customize->add_control('moments_signature', array(
        'label' => __('个性签名', 'kam-inspired'),
        'section' => 'kam_moments_settings',
        'type' => 'text',
    ));
}
add_action('customize_register', 'kam_customize_register');

/**
 * 输出自定义CSS到前端
 */
function kam_custom_css() {
    ?>
    <style type="text/css">
        :root {
            --accent-color: <?php echo get_theme_mod('kam_primary_color', '#ff3e7f'); ?>;
            --bg-dark: <?php echo get_theme_mod('kam_bg_color', '#0a0a0a'); ?>;
            --text-light: <?php echo get_theme_mod('kam_text_color', '#ffffff'); ?>;
        }
        
        /* 图片预览网格 */
        .image-preview-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 5px;
            margin-top: 10px;
        }
        
        /* 登录提示样式 */
        .login-prompt {
            background: #f9f9f9;
            padding: 20px;
            text-align: center;
            color: #666;
            border-radius: 8px;
        }
        
        .login-prompt a {
            color: #07C160;
            text-decoration: none;
        }
        
        /* 评论表单样式调整 */
        #commentform {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        #commentform .comment-text {
            flex: 1;
            border: 1px solid #ddd;
            border-radius: 20px;
            padding: 8px 15px;
            outline: none;
            font-size: 0.9rem;
        }
        
        #commentform .comment-submit {
            background: #07C160;
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 20px;
            cursor: pointer;
            font-size: 0.9rem;
        }
        
        /* 表情面板样式 */
        .emoji-panel {
            position: absolute;
            z-index: 100;
        }
        
        /* 位置输入框样式 */
        .location-input input {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        
        /* 动态位置信息 */
        .moment-location {
            font-size: 0.8rem;
            color: #666;
            margin-bottom: 2px;
        }
    </style>
    <?php
}
add_action('wp_head', 'kam_custom_css');

// AJAX处理点赞功能
function kam_handle_moment_like() {
    $moment_id = intval($_POST['moment_id']);
    
    // 对于未登录用户，使用IP地址作为标识
    $identifier = is_user_logged_in() ? get_current_user_id() : $_SERVER['REMOTE_ADDR'];
    
    $likes = get_post_meta($moment_id, 'moment_likes', true) ?: array();
    $liked = in_array($identifier, $likes);
    
    if ($liked) {
        // 取消点赞
        $likes = array_diff($likes, array($identifier));
    } else {
        // 点赞
        $likes[] = $identifier;
    }
    
    update_post_meta($moment_id, 'moment_likes', $likes);
    
    wp_send_json_success(array(
        'liked' => !$liked,
        'like_count' => count($likes)
    ));
}
add_action('wp_ajax_moment_like', 'kam_handle_moment_like');
add_action('wp_ajax_nopriv_moment_like', 'kam_handle_moment_like');

// AJAX处理评论功能
function kam_handle_moment_comment() {
    $moment_id = intval($_POST['moment_id']);
    $content = sanitize_text_field($_POST['content']);
    $name = sanitize_text_field($_POST['name']);
    $email = sanitize_email($_POST['email']);
    $user_id = get_current_user_id();
    
    if (!$user_id) {
        wp_send_json_error('请先登录');
    }
    
    if (empty($content)) {
        wp_send_json_error('评论内容不能为空');
    }
    
    if (empty($name)) {
        wp_send_json_error('请输入昵称');
    }
    
    if (empty($email) || !is_email($email)) {
        wp_send_json_error('请输入有效的邮箱地址');
    }
    // 新增：验证nonce
    if (!wp_verify_nonce($_POST['nonce'], 'moment_comment_nonce')) {
        wp_send_json_error('安全验证失败', 403); // 验证失败返回403
    }
    
    $moment_id = intval($_POST['moment_id']);
    $content = sanitize_text_field($_POST['content']);
    $user_id = get_current_user_id();
    
    if (!$user_id) {
        wp_send_json_error('请先登录'); // 仍可限制登录后评论
    }
    $comment = array(
        'author' => $name,
        'email' => $email,
        'content' => $content,
        'time' => current_time('mysql')
    );
    
    $comments = get_post_meta($moment_id, 'moment_comments', true) ?: array();
    $comments[] = $comment;
    
    update_post_meta($moment_id, 'moment_comments', $comments);
    
    wp_send_json_success(array(
        'comment' => $comment,
        'comment_count' => count($comments)
    ));
}
// 原有登录用户钩子
add_action('wp_ajax_moment_comment', 'kam_handle_moment_comment');
// 新增未登录用户钩子（即使后端验证登录，也需注册否则返回400）
add_action('wp_ajax_nopriv_moment_comment', 'kam_handle_moment_comment');

// 发布动态处理
function kam_handle_publish_moment() {
    if (!is_user_logged_in()) {
        wp_send_json_error('请先登录');
    }
    
    $content = sanitize_text_field($_POST['content']);
    $location = sanitize_text_field($_POST['location']);
    $images = json_decode(stripslashes($_POST['images']), true);
    
    if (empty($content) && empty($images)) {
        wp_send_json_error('内容不能为空');
    }
    
    // 创建新动态
    $post_id = wp_insert_post(array(
        'post_type' => 'moment',
        'post_title' => '动态 ' . date('Y-m-d H:i:s'),
        'post_content' => $content,
        'post_status' => 'publish',
        'post_author' => get_current_user_id()
    ));
    
    if ($post_id) {
        // 保存位置信息
        if (!empty($location)) {
            update_post_meta($post_id, 'moment_location', $location);
        }
        
        // 保存图片
        if (!empty($images)) {
            $image_urls = array();
            foreach ($images as $image_data) {
                // 这里简化处理，实际应用中应该保存图片到服务器
                $image_urls[] = $image_data;
            }
            update_post_meta($post_id, 'moment_images', implode(',', $image_urls));
        }
        
        // 保存发布时间
        update_post_meta($post_id, 'moment_date', current_time('mysql'));
        
        wp_send_json_success(array('post_id' => $post_id));
    } else {
        wp_send_json_error('发布失败');
    }
}
add_action('wp_ajax_publish_moment', 'kam_handle_publish_moment');

// 创建示例朋友圈动态
function kam_create_sample_moments() {
    // 检查是否已经创建过示例数据
    if (get_option('kam_sample_moments_created')) {
        return;
    }
    
    $sample_moments = array(
        array(
            'title' => '今天天气真好',
            'content' => '阳光明媚，适合出去走走 🌞',
            'author' => 1
        ),
        array(
            'title' => '分享一首好听的歌',
            'content' => '最近在听周杰伦的新歌，太好听了！🎵',
            'author' => 1
        ),
        array(
            'title' => '学习WordPress开发',
            'content' => '今天学习了WordPress主题开发，收获满满！💻',
            'author' => 1
        )
    );
    
    foreach ($sample_moments as $moment) {
        $post_id = wp_insert_post(array(
            'post_type' => 'moment',
            'post_title' => $moment['title'],
            'post_content' => $moment['content'],
            'post_status' => 'publish',
            'post_author' => $moment['author']
        ));
        
        if ($post_id) {
            update_post_meta($post_id, 'moment_date', current_time('mysql'));
        }
    }
    
    update_option('kam_sample_moments_created', true);
}
// 仅在主题激活时创建示例数据
// register_activation_hook(__FILE__, 'kam_create_sample_moments');