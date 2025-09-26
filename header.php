<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php 
        if (is_home() || is_front_page()) {
            echo '朋友圈 - ' . get_bloginfo('name');
        } else {
            wp_title(' - ', true, 'right');
            bloginfo('name');
        }
    ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php if (!is_home() && !is_front_page()) : ?>
<header class="main-header">
    <nav class="main-nav">
        <div class="container">
            <div class="nav-brand">
                <?php 
                $custom_logo = get_theme_mod('kam_logo');
                if ($custom_logo) : ?>
                    <a href="<?php echo home_url(); ?>">
                        <img src="<?php echo $custom_logo; ?>" alt="<?php bloginfo('name'); ?>" class="site-logo">
                    </a>
                <?php else : ?>
                    <a href="<?php echo home_url(); ?>"><?php bloginfo('name'); ?></a>
                <?php endif; ?>
            </div>
            
            <div class="nav-menu">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class' => 'nav-links',
                    'container' => false
                ));
                ?>
            </div>
            
            <div class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </nav>
</header>
<?php endif; ?>