<?php
/**
 * Template Name: 联系页面
 */
get_header(); ?>

<main class="contact-main">
    <div class="container">
        <section class="contact-hero">
            <h1>联系我们</h1>
            <p>有兴趣合作？让我们开始对话</p>
        </section>
        
        <div class="contact-grid">
            <div class="contact-info">
                <h2>联系方式</h2>
                <div class="contact-items">
                    <div class="contact-item">
                        <h3>邮箱</h3>
                        <p><?php echo antispambot(get_theme_mod('kam_contact_email', 'hello@example.com')); ?></p>
                    </div>
                    <div class="contact-item">
                        <h3>电话</h3>
                        <p><?php echo esc_html(get_theme_mod('kam_contact_phone', '+86 138 0000 0000')); ?></p>
                    </div>
                    <div class="contact-item">
                        <h3>地址</h3>
                        <p><?php echo esc_html(get_theme_mod('kam_contact_address', '中国某市某区某街道')); ?></p>
                    </div>
                    <div class="contact-item">
                        <h3>工作时间</h3>
                        <p>周一至周五: 9:00 - 18:00<br>周末: 预约制</p>
                    </div>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>发送消息</h2>
                <?php 
                // 检查是否安装了Contact Form 7插件
                if (function_exists('wpcf7_contact_form')) {
                    echo do_shortcode('[contact-form-7 id="123" title="联系表单"]');
                } else {
                    // 使用自定义表单
                ?>
                <form class="custom-contact-form" method="post">
                    <div class="form-group">
                        <input type="text" name="name" placeholder="您的姓名" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" placeholder="邮箱地址" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="subject" placeholder="主题" required>
                    </div>
                    <div class="form-group">
                        <textarea name="message" placeholder="留言内容" rows="5" required></textarea>
                    </div>
                    <button type="submit" class="submit-btn">发送消息</button>
                </form>
                <?php } ?>
            </div>
        </div>

        <section class="map-section">
            <h2>我们的位置</h2>
            <div class="map-placeholder">
                <p>🌍 地图位置显示</p>
                <p>这里可以嵌入Google Maps或百度地图</p>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>