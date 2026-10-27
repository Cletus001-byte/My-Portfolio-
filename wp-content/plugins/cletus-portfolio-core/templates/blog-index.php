<?php
/**
 * Blog Index Editorial Template for Cletus Portfolio
 * Visual Identity: Pure Black (#050505) + Deep Yellow Gold (#D4A017) + White (#FFFFFF)
 */

if (!defined('ABSPATH')) {
    exit;
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog — Amos Egwolo Cletus</title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('cletus-portfolio-theme'); ?>>

<?php
cletus_render_header();

// Query all published WordPress posts dynamically
$posts_query = new WP_Query([
    'post_type'      => 'post',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'date',
    'order'          => 'DESC'
]);
?>

<main class="cletus-main-content">
    <section class="cletus-page-header">
        <div class="cletus-container">
            <span class="cletus-section-kicker">BLOG</span>
            <h1 class="cletus-page-title">Thoughts, Lessons, <span class="cletus-text-gold">& Experiences</span></h1>
            <p class="cletus-page-lead">Thoughts, lessons, tools and experiences from my journey through technology, web development and creative design.</p>
        </div>
    </section>

    <section class="cletus-section">
        <div class="cletus-container">
            <div class="cletus-blog-editorial-grid">
                <?php if ($posts_query->have_posts()) : ?>
                    <?php while ($posts_query->have_posts()) : $posts_query->the_post(); 
                        $post_id   = get_the_ID();
                        $title     = get_the_title();
                        $permalink = get_permalink();
                        $date      = get_the_date('F j, Y');
                        $cats      = wp_get_post_categories($post_id, ['fields' => 'names']);
                        $cat_name  = !empty($cats) ? $cats[0] : 'Technology';
                        
                        // Generate natural excerpt (~32 words)
                        $content_raw = strip_tags(get_the_content());
                        $excerpt     = wp_trim_words($content_raw, 32, '...');
                    ?>
                        <article class="cletus-blog-row">
                            <?php 
                            $thumb_url = cletus_get_post_featured_image_url($post_id);
                            if ($thumb_url) : 
                            ?>
                                <div class="cletus-blog-thumb-box">
                                    <a href="<?php echo esc_url($permalink); ?>" aria-label="<?php echo esc_attr($title); ?>">
                                        <img src="<?php echo esc_url($thumb_url); ?>" 
                                             alt="<?php echo esc_attr($title); ?>" 
                                             class="cletus-blog-thumb-img" 
                                             loading="lazy">
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="cletus-blog-content-box">
                                <div class="cletus-blog-meta-top">
                                    <span class="cletus-blog-category"><?php echo esc_html($cat_name); ?></span>
                                    <span class="cletus-blog-date"><?php echo esc_html($date); ?></span>
                                </div>
                                <h3 class="cletus-blog-title">
                                    <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($title); ?></a>
                                </h3>
                                <p class="cletus-blog-excerpt"><?php echo esc_html($excerpt); ?></p>
                                <div class="cletus-blog-meta-bottom">
                                    <a href="<?php echo esc_url($permalink); ?>" class="cletus-read-article-btn">
                                        <span>Read Article</span>
                                        <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                <?php else : ?>
                    <p class="text-center cletus-text-muted">No published blog posts found.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<?php
cletus_render_footer();
wp_footer();
?>
</body>
</html>
