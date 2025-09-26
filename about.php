<?php
/**
 * Template Name: 关于页面
 */
get_header(); ?>

<main class="about-main">
    <div class="container">
        <section class="about-hero">
            <div class="about-text">
                <h1>关于我们</h1>
                <p class="lead">创造有意义的设计体验</p>
            </div>
        </section>
        
        <section class="about-content">
            <div class="about-grid">
                <div class="about-story">
                    <h2>我们的故事</h2>
                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                        <?php the_content(); ?>
                    <?php endwhile; endif; ?>
                </div>
                
                <div class="about-skills">
                    <h2>专业技能</h2>
                    <div class="skills-list">
                        <div class="skill-item">
                            <span class="skill-name">UI/UX 设计</span>
                            <div class="skill-bar">
                                <div class="skill-progress" data-width="90%"></div>
                            </div>
                        </div>
                        <div class="skill-item">
                            <span class="skill-name">前端开发</span>
                            <div class="skill-bar">
                                <div class="skill-progress" data-width="85%"></div>
                            </div>
                        </div>
                        <div class="skill-item">
                            <span class="skill-name">品牌设计</span>
                            <div class="skill-bar">
                                <div class="skill-progress" data-width="80%"></div>
                            </div>
                        </div>
                        <div class="skill-item">
                            <span class="skill-name">WordPress开发</span>
                            <div class="skill-bar">
                                <div class="skill-progress" data-width="95%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</main>

<?php get_footer(); ?>