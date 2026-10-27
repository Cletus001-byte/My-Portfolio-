<?php
/**
 * Single Blog Post Editorial Template for Cletus Portfolio
 * Visual Identity: Pure Black (#050505) + Deep Yellow Gold (#D4A017) + White (#FFFFFF)
 */

if (!defined('ABSPATH')) {
    exit;
}

$post_id   = get_the_ID();
$title     = get_the_title();
$date      = get_the_date('F j, Y');
$cats      = wp_get_post_categories($post_id, ['fields' => 'names']);
$cat_name  = !empty($cats) ? $cats[0] : 'Technology';
$author    = get_the_author_meta('display_name') ?: 'Amos Egwolo Cletus';
$blog_url  = home_url('/blog/');

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($title); ?> — Amos Egwolo Cletus</title>
    <?php wp_head(); ?>
</head>
<body <?php body_class('cletus-portfolio-theme'); ?>>

<?php
cletus_render_header();
?>

<main class="cletus-main-content">
    <article class="cletus-single-post-wrapper">
        <header class="cletus-single-post-header">
            <div class="cletus-container">
                <a href="<?php echo esc_url($blog_url); ?>" class="cletus-back-link">
                    <?php echo cletus_get_icon_svg('arrow', 'cletus-back-icon'); ?>
                    <span>Back to Blog</span>
                </a>

                <div class="cletus-single-meta-top mt-20">
                    <span class="cletus-section-kicker"><?php echo esc_html($cat_name); ?></span>
                </div>

                <h1 class="cletus-single-post-title"><?php echo esc_html($title); ?></h1>

                <div class="cletus-single-post-byline">
                    <span class="cletus-byline-author">By <strong>Amos Egwolo Cletus</strong></span>
                    <span class="cletus-byline-divider">&bull;</span>
                    <span class="cletus-byline-date"><?php echo esc_html($date); ?></span>
                </div>
            </div>
        </header>

        <section class="cletus-section cletus-single-post-body">
            <div class="cletus-container">
                <div class="cletus-reading-container">
                    <?php 
                    $featured_url = cletus_get_post_featured_image_url($post_id);
                    if ($featured_url) : 
                    ?>
                        <div class="cletus-single-featured-box">
                            <img src="<?php echo esc_url($featured_url); ?>" 
                                 alt="<?php echo esc_attr($title); ?>" 
                                 class="cletus-single-featured-img">
                        </div>
                    <?php endif; ?>

                    <?php
                    while (have_posts()) :
                        the_post();
                        the_content();
                    endwhile;
                    ?>

                    <div class="cletus-post-footer-nav mt-50">
                        <a href="<?php echo esc_url($blog_url); ?>" class="cletus-btn cletus-btn-outline">
                            <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-left'); ?>
                            <span>Back to All Articles</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </article>
</main>

<?php
cletus_render_footer();
wp_footer();
?>
</body>
</html>
