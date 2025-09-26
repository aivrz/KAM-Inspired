<?php if (!is_home() && !is_front_page()) : ?>
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-info">
                <p><?php echo wp_kses_post(get_theme_mod('kam_copyright_text', '&copy; ' . date('Y') . ' ' . get_bloginfo('name') . '. All rights reserved.')); ?></p>
            </div>
            <div class="footer-links">
                <?php
                $social_links = get_theme_mod('kam_social_links');
                if ($social_links) {
                    $links = explode("\n", $social_links);
                    foreach ($links as $link) {
                        $parts = explode(',', trim($link));
                        if (count($parts) === 2) {
                            echo '<a href="' . esc_url(trim($parts[1])) . '">' . esc_html(trim($parts[0])) . '</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</footer>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>