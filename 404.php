<?php get_header(); ?>

<main class="error-main">
    <div class="container">
        <div class="error-content">
            <h1>404</h1>
            <h2>页面未找到</h2>
            <p>抱歉，您访问的页面不存在或已被移动。</p>
            <div class="error-actions">
                <a href="<?php echo home_url(); ?>" class="back-home">返回首页</a>
                <a href="javascript:history.back()" class="back-previous">返回上页</a>
            </div>
            <div class="error-search">
                <p>或者尝试搜索您需要的内容：</p>
                <?php get_search_form(); ?>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?>