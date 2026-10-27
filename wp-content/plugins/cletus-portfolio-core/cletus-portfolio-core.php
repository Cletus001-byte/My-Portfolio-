<?php
/**
 * Plugin Name: Cletus Portfolio Core
 * Plugin URI: http://localhost/cletusportfolio
 * Description: Complete, high-impact personal portfolio engine for Amos Egwolo Cletus — Creative Designer & Web Developer.
 * Version: 3.1.0
 * Author: Amos Egwolo Cletus
 */

if (!defined('ABSPATH')) {
    exit;
}

define('CLETUS_PORTFOLIO_PATH', plugin_dir_path(__FILE__));
define('CLETUS_PORTFOLIO_URL', plugin_dir_url(__FILE__));

// Enqueue Google Fonts, Font Awesome Brands, Custom Stylesheet & JS
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style('google-fonts-cletus', 'https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap', [], null);
    wp_enqueue_style('font-awesome-brands', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');
    wp_enqueue_style('cletus-portfolio-styles', CLETUS_PORTFOLIO_URL . 'assets/css/portfolio.css', [], '5.0.0');
    wp_enqueue_script('cletus-portfolio-scripts', CLETUS_PORTFOLIO_URL . 'assets/js/portfolio.js', [], '5.0.0', false);
});

// Disable Astra smooth-scroll interference on internal navigation links
add_filter('astra_get_option_enable-scroll-to-id', '__return_false');

// Helper: Vector & Image Icons System (Loads local icon files placed by user)
function cletus_get_icon_svg($icon, $extra_class = '') {
    $class = 'cletus-svg-icon ' . esc_attr($extra_class);
    $icon_key = sanitize_file_name($icon);
    
    // Check for PNG icon files in assets/icons/
    $png_file = CLETUS_PORTFOLIO_PATH . 'assets/icons/' . $icon_key . '.png';
    $no_ext_file = CLETUS_PORTFOLIO_PATH . 'assets/icons/' . $icon_key;
    
    if (file_exists($png_file)) {
        $png_url = CLETUS_PORTFOLIO_URL . 'assets/icons/' . $icon_key . '.png';
        return '<img src="' . esc_url($png_url) . '" alt="' . esc_attr($icon_key) . '" class="cletus-png-icon ' . esc_attr($extra_class) . '" width="22" height="22" loading="eager">';
    } elseif (file_exists($no_ext_file) && !is_dir($no_ext_file)) {
        $no_ext_url = CLETUS_PORTFOLIO_URL . 'assets/icons/' . $icon_key;
        return '<img src="' . esc_url($no_ext_url) . '" alt="' . esc_attr($icon_key) . '" class="cletus-png-icon ' . esc_attr($extra_class) . '" width="22" height="22" loading="eager">';
    }

    $svg_file = CLETUS_PORTFOLIO_PATH . 'assets/icons/' . $icon_key . '.svg';
    if (file_exists($svg_file)) {
        $svg_content = file_get_contents($svg_file);
        if ($svg_content !== false) {
            if (strpos($svg_content, 'class=') !== false) {
                $svg_content = preg_replace('/class="[^"]*"/i', 'class="' . $class . '"', $svg_content, 1);
            } else {
                $svg_content = str_replace('<svg ', '<svg class="' . $class . '" ', $svg_content);
            }
            if (strpos($svg_content, 'aria-hidden=') === false) {
                $svg_content = str_replace('<svg ', '<svg aria-hidden="true" ', $svg_content);
            }
            return $svg_content;
        }
    }

    $svgs = [
        'code' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>',
        'design' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 19l7-7 3 3-7 7-3-3z"></path><path d="M18 13l-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"></path><path d="M2 2l7.586 7.586"></path><circle cx="11" cy="11" r="2"></circle></svg>',
        'server' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"></rect><rect x="2" y="14" width="20" height="8" rx="2" ry="2"></rect><line x1="6" y1="6" x2="6.01" y2="6"></line><line x1="6" y1="18" x2="6.01" y2="18"></line></svg>',
        'education' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 10v6M2 10l10-5 10 5-10 5z"></path><path d="M6 12v5c3 3 9 3 12 0v-5"></path></svg>',
        'resume' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>',
        'mail' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>',
        'location' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>',
        'status' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>',
        'arrow' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>',
        'eye' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>',
        'layers' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>',
        'layout' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>',
        'wordpress' => '<svg class="' . $class . '" viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true"><path d="M12.158 12.786l-2.698 7.84c.806.236 1.657.367 2.54.367 2.82 0 5.344-1.282 7.018-3.298-.027-.042-.058-.087-.083-.133l-3.568-9.878c.328-.026.657-.052.657-.052.428-.05.378-.679-.051-.655 0 0-1.294.103-2.128.103-.799 0-2.076-.103-2.076-.103-.429-.024-.479.605-.05.655 0 0 .629.052.932.078l2.062 5.679-2.88 8.6-4.133-12.357c.328-.026.656-.052.656-.052.429-.05.379-.679-.05-.655 0 0-1.294.103-2.129.103-.799 0-2.076-.103-2.076-.103-.429-.024-.479.605-.05.655 0 0 .628.052.932.078l5.632 16.331zm-7.61-7.142c-1.344 1.704-2.148 3.856-2.148 6.206 0 3.323 1.597 6.272 4.054 8.113l-4.22-12.28c.106-.696.195-1.385.314-2.039zm14.904-.694c.371.745.602 1.62.602 2.628 0 1.69-.933 3.018-1.782 4.296-.653.957-1.332 1.942-1.332 3.109 0 .848.337 1.55.806 2.222l2.457-7.118c.365-.953.518-1.742.518-2.476 0-.931-.26-1.803-.707-2.551zM12 0C5.373 0 0 5.37 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm0 22.8c-5.955 0-10.8-4.845-10.8-10.8S6.045 1.2 12 1.2s10.8 4.845 10.8 10.8-4.845 10.8-10.8 10.8z"/></svg>',
    ];

    return $svgs[$icon] ?? $svgs['code'];
}

// Master Social Media Links Configuration
function cletus_get_social_links() {
    return [
        'tiktok' => [
            'name' => 'TikTok',
            'url' => 'https://www.tiktok.com/@amoscletus?_r=1&_t=ZS-9A0I7Wptc9N',
            'handle' => '@amoscletus',
            'icon' => 'tiktok',
            'label' => 'TikTok (@amoscletus)',
        ],
        'instagram' => [
            'name' => 'Instagram',
            'url' => 'https://www.instagram.com/clefixgraphix?stkn=MW9nNWYwNTRqOGpqeg==',
            'handle' => '@clefixgraphix',
            'icon' => 'instagram',
            'label' => 'Instagram (@clefixgraphix)',
        ],
        'facebook' => [
            'name' => 'Facebook',
            'url' => 'https://www.facebook.com/share/1JAYshMmG1/',
            'handle' => 'Facebook Profile',
            'icon' => 'facebook',
            'label' => 'Facebook Profile',
        ],
        'whatsapp' => [
            'name' => 'WhatsApp Business',
            'url' => 'https://wa.me/message/7WBKWEIFFIZQP1',
            'handle' => 'WhatsApp Business',
            'icon' => 'whatsapp',
            'label' => 'WhatsApp Business',
        ],
        'github' => [
            'name' => 'GitHub',
            'url' => 'https://github.com',
            'handle' => 'GitHub Profile',
            'icon' => 'github',
            'label' => 'GitHub Profile',
        ],
    ];
}

// Helper: SVG Placeholders System
function cletus_get_placeholder_svg($name, $width = 600, $height = 380, $icon_type = 'code') {
    $title = esc_html($name);
    $icons = [
        'profile' => '<circle cx="50%" cy="40%" r="45" fill="none" stroke="#D4A017" stroke-width="3"/><path d="M -60 140 Q 0 70 60 140" fill="none" stroke="#D4A017" stroke-width="3" transform="translate(150, 110)"/>',
        'code' => '<path d="M -30 -20 L -60 0 L -30 20 M 30 -20 L 60 0 L 30 20 M -10 25 L 10 -25" fill="none" stroke="#D4A017" stroke-width="3" transform="translate(' . ($width/2) . ', ' . ($height/2 - 20) . ')"/>',
        'server' => '<rect x="-50" y="-30" width="100" height="25" rx="4" fill="none" stroke="#D4A017" stroke-width="2" transform="translate(' . ($width/2) . ', ' . ($height/2 - 30) . ')"/><rect x="-50" y="5" width="100" height="25" rx="4" fill="none" stroke="#D4A017" stroke-width="2" transform="translate(' . ($width/2) . ', ' . ($height/2 - 30) . ')"/>',
        'design' => '<circle cx="0" cy="-20" r="14" fill="none" stroke="#D4A017" stroke-width="2.5" transform="translate(' . ($width/2) . ', ' . ($height/2 - 10) . ')"/><path d="M -25 15 L 25 -15 M -15 -25 L 15 25" stroke="#D4A017" stroke-width="2.5" transform="translate(' . ($width/2) . ', ' . ($height/2 - 10) . ')"/>',
        'blog' => '<rect x="-40" y="-30" width="80" height="60" rx="4" fill="none" stroke="#D4A017" stroke-width="2" transform="translate(' . ($width/2) . ', ' . ($height/2 - 10) . ')"/><line x1="-25" y1="-15" x2="25" y2="-15" stroke="#D4A017" stroke-width="2" transform="translate(' . ($width/2) . ', ' . ($height/2 - 10) . ')"/><line x1="-25" y1="0" x2="15" y2="0" stroke="#8A8A8A" stroke-width="2" transform="translate(' . ($width/2) . ', ' . ($height/2 - 10) . ')"/>',
    ];
    $icon_svg = $icons[$icon_type] ?? $icons['code'];

    return 'data:image/svg+xml;utf8,' . rawurlencode('
        <svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">
            <rect width="100%" height="100%" fill="#111111"/>
            <rect x="2" y="2" width="' . ($width - 4) . '" height="' . ($height - 4) . '" fill="none" stroke="#292929" stroke-width="2" rx="8"/>
            <rect x="12" y="12" width="' . ($width - 24) . '" height="' . ($height - 24) . '" fill="none" stroke="#D4A017" stroke-opacity="0.2" stroke-dasharray="6,6" rx="6"/>
            ' . $icon_svg . '
            <text x="50%" y="' . ($height - 40) . '" fill="#FFFFFF" font-family="Space Grotesk, sans-serif" font-size="13" font-weight="600" text-anchor="middle" letter-spacing="1">' . $title . '</text>
            <text x="50%" y="' . ($height - 20) . '" fill="#8A8A8A" font-family="Space Grotesk, sans-serif" font-size="10" text-anchor="middle">Click or edit to replace image asset</text>
        </svg>
    ');
}

// Master Portfolio Projects Configuration (Actual Project Asset Images Connected)
function cletus_get_portfolio_projects() {
    $base_img_url = CLETUS_PORTFOLIO_URL . 'assets/images/';
    return [
        'stockconnect' => [
            'num' => '01',
            'id' => 'STOCKCONNECT_IMAGE_PLACEHOLDER',
            'image_url' => $base_img_url . 'stockconnect.png',
            'title' => 'StockConnect',
            'category' => 'Web Application / Fintech',
            'badge' => 'Featured Platform',
            'desc' => 'StockConnect is a digital livestock marketplace that connects customers with reliable livestock sellers, making it easier to discover, purchase, and access livestock through a convenient digital platform.',
            'problem' => 'Finding reliable livestock can be difficult because of distance, limited seller information, and difficulty identifying trustworthy sources.',
            'solution' => 'StockConnect connects customers with livestock sellers through a secure digital marketplace, making livestock discovery, buying, and distribution more accessible and convenient.',
            'tags' => ['HTML', 'CSS', 'JavaScript', 'Laravel', 'XAMPP'],
            'icon_type' => 'code',
            'action_text' => 'View Project Details',
            'action_icon' => 'arrow',
            'layout_style' => 'staggered-left',
        ],
        'vecora' => [
            'num' => '02',
            'id' => 'VECORA_IMAGE_PLACEHOLDER',
            'image_url' => $base_img_url . 'vecora.png',
            'title' => 'Vecora Technologies',
            'category' => 'Web Development / WordPress',
            'badge' => 'Enterprise System',
            'desc' => 'Vecora Technologies is a web hosting and digital technology platform for hosting and managing websites, domains, DNS, and virtual hosting resources.',
            'problem' => 'Website owners can struggle to find reliable hosting and manage websites, domains, DNS, and hosting resources from one place.',
            'solution' => 'Vecora provides a centralized hosting environment that makes website hosting, resource management, and DNS configuration easier to manage.',
            'tags' => ['WordPress', 'PHP', 'JavaScript', 'CSS'],
            'icon_type' => 'server',
            'action_text' => 'View Project Details',
            'action_icon' => 'arrow',
            'layout_style' => 'staggered-right',
        ],
        'creative_designs' => [
            'num' => '03',
            'id' => 'CREATIVE_DESIGNS_IMAGE_PLACEHOLDER',
            'title' => 'Creative Designs',
            'category' => 'Creative Design',
            'badge' => 'Showcase Collection',
            'desc' => 'A curated collection and showcase of creative design work including graphic designs, promotional flyers, event posters, social media graphics, corporate branding assets, and visual UI layouts.',
            'tags' => ['Photoshop', 'Canva', 'CorelDRAW'],
            'icon_type' => 'design',
            'action_text' => 'Explore Designs',
            'action_icon' => 'eye',
            'layout_style' => 'hero-showcase',
        ],
        'studenthub' => [
            'num' => '01',
            'id' => 'STUDENTHUB_IMAGE',
            'image_url' => $base_img_url . 'studenthub.jpg',
            'title' => 'StudentHub',
            'category' => 'Digital Education Platform',
            'badge' => 'Future Project Idea',
            'desc' => 'A digital platform that helps students access useful resources, opportunities, collaboration and academic support.',
            'problem' => 'Students often struggle to access centralized academic resources, internship opportunities, and collaborative support tools in one structured place.',
            'solution' => 'A digital platform unifying educational materials, peer collaboration channels, career opportunities, and academic support services.',
            'whats_next' => 'Conducting student community feedback sessions to refine feature wireframes and user workflow designs.',
            'tags' => ['Education Tech', 'Student Support', 'Resource Hub', 'Collaboration'],
            'icon_type' => 'code',
            'layout_style' => 'staggered-left',
        ],
        'local_business_hub' => [
            'num' => '02',
            'id' => 'LOCAL_BUSINESS_HUB_IMAGE',
            'image_url' => $base_img_url . 'local-business-hub.jpg',
            'title' => 'Local Business Digital Hub',
            'category' => 'Business Technology & E-Commerce',
            'badge' => 'Future Project Idea',
            'desc' => 'A digital platform that helps small businesses establish an online presence, showcase their products/services and connect with customers.',
            'problem' => 'Small businesses frequently lack affordable technical solutions to build an online presence, display products, and engage local customers online.',
            'solution' => 'An intuitive digital hub empowering small businesses to create digital profiles, list services, showcase products, and connect directly with clients.',
            'whats_next' => 'Developing an interactive MVP interface for business profile registration and service showcase management.',
            'tags' => ['Small Business', 'Digital Marketplace', 'Online Presence', 'Customer Connect'],
            'icon_type' => 'server',
            'layout_style' => 'staggered-right',
        ],
        'skillbridge' => [
            'num' => '03',
            'id' => 'SKILLBRIDGE_IMAGE',
            'image_url' => $base_img_url . 'skillbridge.jpg',
            'title' => 'SkillBridge',
            'category' => 'Skills & Career Platform',
            'badge' => 'Future Project Idea',
            'desc' => 'A platform connecting students with practical projects, skills development, internships and other opportunities.',
            'problem' => 'Students often lack practical, real-world project experience required by employers, while organizations struggle to find eager entry-level talent.',
            'solution' => 'A platform bridging the experience gap by matching students with real hands-on projects, skill development tasks, and internship roles.',
            'whats_next' => 'Designing the skill-matching algorithm and interactive student-organization project dashboard.',
            'tags' => ['Skill Building', 'Internship Match', 'Project Experience', 'Career Growth'],
            'icon_type' => 'design',
            'layout_style' => 'staggered-left',
        ],
    ];
}

// 12 Creative Design Images Dataset Function (Serves High-Performance Web Optimized Copies)
function cletus_get_creative_design_images() {
    $images = [];
    $opt_dir_path = CLETUS_PORTFOLIO_PATH . 'assets/images/optimized/';
    $opt_dir_url  = CLETUS_PORTFOLIO_URL . 'assets/images/optimized/';
    $base_dir_url = CLETUS_PORTFOLIO_URL . 'assets/images/';

    for ($i = 1; $i <= 12; $i++) {
        // Load optimized web copy if available, fallback to original high-res asset
        $url = file_exists($opt_dir_path . $i . '.jpg') ? ($opt_dir_url . $i . '.jpg') : ($base_dir_url . $i . '.jpg');
        $images[] = [
            'url' => $url,
            'alt' => 'Creative Design Artwork ' . $i,
            'num' => sprintf('%02d', $i)
        ];
    }
    return $images;
}

// Render Continuous Horizontal Moving Showcase for Creative Designs (Marquee Reel Inside Fixed Project Box)
function cletus_render_creative_designs_showcase($show_button = true) {
    $creative_images = cletus_get_creative_design_images();
    ob_start();
    ?>
    <div class="cletus-showcase-row cletus-stagger-left">
        <!-- FIXED PROJECT DISPLAY BOX (Matches StockConnect & Vecora Size) -->
        <div class="cletus-showcase-img-box cletus-creative-box-viewport" id="creative-designs-project-box">
            <span class="cletus-showcase-badge">Design Reel</span>
            <div class="cletus-marquee-track">
                <!-- Original 12 Creative Images (Eager + Async Decoding for Zero-Stutter Smooth Reel) -->
                <?php foreach ($creative_images as $img) : ?>
                    <div class="cletus-marquee-item">
                        <img src="<?php echo esc_url($img['url']); ?>" 
                             alt="<?php echo esc_attr($img['alt']); ?>" 
                             loading="eager" 
                             decoding="async">
                        <div class="cletus-marquee-overlay">
                            <span>Design <?php echo esc_html($img['num']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>

                <!-- Duplicated 12 Creative Images for 100% Seamless Infinite Loop -->
                <?php foreach ($creative_images as $img) : ?>
                    <div class="cletus-marquee-item" aria-hidden="true">
                        <img src="<?php echo esc_url($img['url']); ?>" 
                             alt="<?php echo esc_attr($img['alt']); ?>" 
                             loading="eager" 
                             decoding="async">
                        <div class="cletus-marquee-overlay">
                            <span>Design <?php echo esc_html($img['num']); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="cletus-showcase-text-box">
            <span class="cletus-showcase-num">03</span>
            <span class="cletus-showcase-cat">CREATIVE DESIGN SHOWCASE</span>
            <h3 class="cletus-showcase-title">Creative Designs</h3>
            <p class="cletus-showcase-desc">A curated collection of creative design work including graphic designs, promotional flyers, event posters, social media graphics, corporate branding assets, and visual UI layouts.</p>
            <div class="cletus-showcase-tech">
                <span>Photoshop</span>
                <span>Canva</span>
                <span>CorelDRAW</span>
            </div>
            <?php if ($show_button) : ?>
                <a href="<?php echo esc_url(home_url('/portfolio-projects/')); ?>" class="cletus-btn cletus-btn-outline cletus-btn-sm">
                    <span>Explore Designs</span>
                    <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Global Header Component
function cletus_render_header() {
    $home_url = home_url('/');
    $about_url = home_url('/about-me/');
    $services_url = home_url('/services-skills/');
    $portfolio_url = home_url('/portfolio-projects/');
    $blog_url = home_url('/blog/');
    $contact_url = home_url('/contact/');
    $logo_url = CLETUS_PORTFOLIO_URL . 'assets/images/amos-egwolo-cletus-logo.png';

    $request_uri = $_SERVER['REQUEST_URI'] ?? '/';
    $raw_path = parse_url($request_uri, PHP_URL_PATH) ?? '/';
    $path = trim($raw_path, '/');
    $clean_path = preg_replace('#^cletusportfolio/?#i', '', $path);
    $clean_path = trim($clean_path, '/');

    $is_home     = ($clean_path === '' || $clean_path === 'home' || is_front_page());
    $is_about    = (strpos($clean_path, 'about') === 0 || is_page('about-me') || is_page(13));
    $is_services = (strpos($clean_path, 'services') === 0 || is_page('services-skills') || is_page(14));
    $is_projects = (strpos($clean_path, 'portfolio-projects') === 0 || strpos($clean_path, 'projects') === 0 || is_page('portfolio-projects') || is_page(15));
    $is_blog     = (strpos($clean_path, 'blog') === 0 || (is_home() && !is_front_page()) || is_page('blog') || is_page(16) || is_single() || is_category() || is_tag());
    $is_contact  = (strpos($clean_path, 'contact') === 0 || is_page('contact') || is_page(17));

    if (is_front_page() || $clean_path === '') {
        $is_home = true;
        $is_about = false;
        $is_services = false;
        $is_projects = false;
        $is_blog = false;
        $is_contact = false;
    }

    ?>
    <!-- Global Full-Website Dynamic Grid Canvas -->
    <canvas class="cletus-grid-canvas" id="cletus-grid-canvas"></canvas>

    <header class="cletus-site-header" id="cletus-header">
        <div class="cletus-nav-container">
            <a href="<?php echo esc_url($home_url); ?>" class="cletus-brand-logo" aria-label="Amos Egwolo Cletus Logo">
                <img src="<?php echo esc_url($logo_url); ?>" 
                     alt="Amos Egwolo Cletus Brand Logo" 
                     class="cletus-main-logo-img">
            </a>

            <nav class="cletus-nav-menu" id="cletus-nav-menu">
                <ul class="cletus-nav-list">
                    <li><a href="<?php echo esc_url($home_url); ?>" class="cletus-nav-link <?php echo $is_home ? 'cletus-nav-active' : ''; ?>">Home</a></li>
                    <li><a href="<?php echo esc_url($about_url); ?>" class="cletus-nav-link <?php echo $is_about ? 'cletus-nav-active' : ''; ?>">About</a></li>
                    <li><a href="<?php echo esc_url($services_url); ?>" class="cletus-nav-link <?php echo $is_services ? 'cletus-nav-active' : ''; ?>">Services</a></li>
                    <li><a href="<?php echo esc_url($portfolio_url); ?>" class="cletus-nav-link <?php echo $is_projects ? 'cletus-nav-active' : ''; ?>">Project / Ideas</a></li>
                    <li><a href="<?php echo esc_url($blog_url); ?>" class="cletus-nav-link <?php echo $is_blog ? 'cletus-nav-active' : ''; ?>">Blog</a></li>
                    <li><a href="<?php echo esc_url($contact_url); ?>" class="cletus-nav-link <?php echo $is_contact ? 'cletus-nav-active' : ''; ?>">Contact</a></li>
                    <li class="cletus-nav-mobile-only">
                        <a href="https://wa.me/message/7WBKWEIFFIZQP1" target="_blank" rel="noopener noreferrer" class="cletus-nav-link cletus-text-gold">
                            <?php echo cletus_get_icon_svg('whatsapp'); ?>
                            <span>WhatsApp Business</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="cletus-header-actions">
                <a href="https://wa.me/message/7WBKWEIFFIZQP1" target="_blank" rel="noopener noreferrer" class="cletus-btn cletus-btn-outline cletus-btn-sm cletus-header-whatsapp" aria-label="WhatsApp Business">
                    <?php echo cletus_get_icon_svg('whatsapp'); ?>
                    <span>WhatsApp</span>
                </a>
                <a href="<?php echo esc_url($contact_url); ?>" class="cletus-btn cletus-btn-primary cletus-btn-sm cletus-header-talk">Let's Talk</a>
                <button class="cletus-hamburger" id="cletus-hamburger" aria-label="Toggle Navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>
    <?php
}

// Global Footer Component
function cletus_render_footer() {
    $home_url = home_url('/');
    $about_url = home_url('/about-me/');
    $portfolio_url = home_url('/portfolio-projects/');
    $contact_url = home_url('/contact/');
    $logo_url = CLETUS_PORTFOLIO_URL . 'assets/images/amos-egwolo-cletus-logo.png';
    ?>
    <footer class="cletus-site-footer">
        <div class="cletus-footer-top">
            <div class="cletus-footer-brand">
                <a href="<?php echo esc_url($home_url); ?>" class="cletus-brand-logo" aria-label="Amos Egwolo Cletus Logo">
                    <img src="<?php echo esc_url($logo_url); ?>" 
                         alt="Amos Egwolo Cletus Brand Logo" 
                         class="cletus-main-logo-img">
                </a>
                <p class="cletus-footer-tagline">
                    Creative Designer & Web Developer | Computer Science Student at SDU. Building scalable tech platforms, modern brand identities, and high-impact digital experiences.
                </p>
            </div>

            <div class="cletus-footer-links-group">
                <div class="cletus-footer-col">
                    <h4 class="cletus-footer-title">Navigation</h4>
                    <ul>
                        <li><a href="<?php echo esc_url($home_url); ?>">Home</a></li>
                        <li><a href="<?php echo esc_url($about_url); ?>">About Me</a></li>
                        <li><a href="<?php echo esc_url(home_url('/services-skills/')); ?>">Services & Skills</a></li>
                        <li><a href="<?php echo esc_url($portfolio_url); ?>">Project / Ideas</a></li>
                        <li><a href="<?php echo esc_url(home_url('/blog/')); ?>">Blog</a></li>
                        <li><a href="<?php echo esc_url($contact_url); ?>">Contact</a></li>
                    </ul>
                </div>

                <div class="cletus-footer-col">
                    <h4 class="cletus-footer-title">Featured Work</h4>
                    <ul>
                        <li><a href="<?php echo esc_url($portfolio_url); ?>">StockConnect</a></li>
                        <li><a href="<?php echo esc_url($portfolio_url); ?>">Vecora Technologies</a></li>
                        <li><a href="<?php echo esc_url($portfolio_url); ?>">Creative Designs</a></li>
                    </ul>
                </div>

                <div class="cletus-footer-col">
                    <h4 class="cletus-footer-title">Social Networks</h4>
                    <ul class="cletus-social-icon-list">
                        <?php foreach (cletus_get_social_links() as $key => $social) : ?>
                            <li>
                                <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?>">
                                    <?php echo cletus_get_icon_svg($social['icon']); ?>
                                    <span><?php echo esc_html($social['name']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="cletus-footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Amos Egwolo Cletus. All rights reserved.</p>
            <p class="cletus-footer-built">Designed & Developed with Precision</p>
        </div>
    </footer>
    <?php
}

// --------------------------------------------------------------------------
// SHORTCODES FOR ALL PAGES
// --------------------------------------------------------------------------

// 1. HOME PAGE SHORTCODE [cletus_home]
add_shortcode('cletus_home', function() {
    ob_start();
    cletus_render_header();
    $profile_image_url = CLETUS_PORTFOLIO_URL . 'assets/images/cletus-image.jpg';
    $projects = cletus_get_portfolio_projects();
    ?>
    <main class="cletus-main-content">
        <!-- Hero Section: Spacious Editorial Layout with Simple Square Frame -->
        <section class="cletus-hero-section">
            <div class="cletus-hero-inner">
                <div class="cletus-hero-text">
                    <div class="cletus-status-badge">
                        <span class="cletus-badge-dot"></span>
                        <span>Available for Web Development & Designs</span>
                    </div>

                    <h1 class="cletus-hero-title">
                        Hi, I'm <span class="cletus-text-gold">Amos Egwolo Cletus</span>
                    </h1>

                    <h2 class="cletus-hero-subtitle">
                        I am a <span id="cletus-typing-text" class="cletus-typing-highlight"></span><span class="cletus-cursor">|</span>
                    </h2>

                    <p class="cletus-hero-description">
                        Computer Science student at SDU, creative designer, and full-stack web developer crafting scalable web applications, modern brand identities, and high-impact digital experiences.
                    </p>

                    <div class="cletus-hero-buttons">
                        <a href="<?php echo esc_url(home_url('/portfolio-projects/')); ?>" class="cletus-btn cletus-btn-primary">
                            <span>View My Work</span>
                            <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                        </a>
                        <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cletus-btn cletus-btn-outline">
                            <span>Let's Work Together</span>
                        </a>
                    </div>

                    <div class="cletus-hero-stats">
                        <div class="cletus-stat-box">
                            <span class="cletus-stat-number">03</span>
                            <span class="cletus-stat-text">Core Disciplines</span>
                        </div>
                        <div class="cletus-stat-box">
                            <span class="cletus-stat-number">CS</span>
                            <span class="cletus-stat-text">SDU Student</span>
                        </div>
                        <div class="cletus-stat-box">
                            <span class="cletus-stat-number">100%</span>
                            <span class="cletus-stat-text">Precision & Focus</span>
                        </div>
                    </div>
                </div>

                <!-- Simple Square Profile Image Frame -->
                <div class="cletus-hero-square-area">
                    <div class="cletus-profile-frame-square" id="cletus-ac-card">
                        <div class="cletus-square-img-wrapper">
                            <img src="<?php echo esc_url($profile_image_url); ?>" 
                                 alt="Amos Egwolo Cletus Photograph" 
                                 class="cletus-profile-square-img" 
                                 id="cletus-hero-profile-img">
                            <div class="cletus-square-corner-accent top-left"></div>
                            <div class="cletus-square-corner-accent bottom-right"></div>
                        </div>
                        <div class="cletus-profile-square-meta">
                            <span class="cletus-profile-square-name">AMOS EGWOLO CLETUS</span>
                            <span class="cletus-profile-square-role">Creative Designer & Web Developer</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Editorial About Teaser -->
        <section class="cletus-section cletus-bg-secondary">
            <div class="cletus-container">
                <div class="cletus-editorial-header">
                    <span class="cletus-section-kicker">01 / BIOGRAPHY & PERSPECTIVE</span>
                    <h2 class="cletus-section-heading">Driven by Curiosity, <span class="cletus-text-gold">Built with Precision</span></h2>
                    <p class="cletus-section-lead">
                        I bridge the gap between technical computer science principles and intuitive creative design.
                    </p>
                </div>

                <div class="cletus-editorial-three-col">
                    <div class="cletus-editorial-col">
                        <span class="cletus-editorial-num">01</span>
                        <h3>Computer Science</h3>
                        <p>Studying Computer Science at SDU, focusing on software algorithms, database management, and robust application architecture.</p>
                    </div>
                    <div class="cletus-editorial-col">
                        <span class="cletus-editorial-num">02</span>
                        <h3>Creative Design</h3>
                        <p>Crafting sleek visual identities, promotional graphics, flyers, posters, and user-centric interfaces using Photoshop and Canva.</p>
                    </div>
                    <div class="cletus-editorial-col">
                        <span class="cletus-editorial-num">03</span>
                        <h3>Web Development</h3>
                        <p>Specializing in custom WordPress development, PHP backend services, Laravel applications, and responsive client interfaces.</p>
                    </div>
                </div>

                <div class="mt-50">
                    <a href="<?php echo esc_url(home_url('/about-me/')); ?>" class="cletus-btn cletus-btn-outline">
                        <span>Read Full Biography</span>
                        <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                    </a>
                </div>
            </div>
        </section>

        <!-- Editorial Capabilities List -->
        <section class="cletus-section">
            <div class="cletus-container">
                <div class="cletus-editorial-header">
                    <span class="cletus-section-kicker">02 / CAPABILITIES & SPECIALIZATIONS</span>
                    <h2 class="cletus-section-heading">Services & <span class="cletus-text-gold">Expertise</span></h2>
                </div>

                <div class="cletus-services-editorial-list">
                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">01</div>
                        <div class="cletus-service-row-body">
                            <h3>Creative Graphic Design</h3>
                            <p>Graphic design, flyer creation, branding collateral, posters, and visual media tailored to elevate brand identity.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('design', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">02</div>
                        <div class="cletus-service-row-body">
                            <h3>Full-Stack Web Development</h3>
                            <p>Clean, performant HTML5, CSS3, JavaScript, PHP, and Laravel web applications tailored for speed, reliability, and security.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('code', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">03</div>
                        <div class="cletus-service-row-body">
                            <h3>Frontend Client Interfaces</h3>
                            <p>Interactive web interfaces built with smooth micro-animations, dark-mode themes, responsive grid systems, and fast execution.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('layout', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">04</div>
                        <div class="cletus-service-row-body">
                            <h3>UI & Visual System Architecture</h3>
                            <p>User-centered wireframing, layout structuring, and dark-themed UI components built for intuitive navigation.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('layers', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">05</div>
                        <div class="cletus-service-row-body">
                            <h3>WordPress & MariaDB Engineering</h3>
                            <p>Custom WordPress implementations, PHP theme integrations, MariaDB database setup, and Elementor layout page building.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('wordpress', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">06</div>
                        <div class="cletus-service-row-body">
                            <h3>Responsive Layout Optimization</h3>
                            <p>Flawless layout adaptation across desktop displays, laptops, tablets, and mobile smartphones.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('server', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Project Showcase Section (Staggered Projects + Continuous Marquee Reel for Creative Designs) -->
        <section class="cletus-section cletus-bg-secondary">
            <div class="cletus-container">
                <div class="cletus-editorial-header">
                    <span class="cletus-section-kicker">03 / PORTFOLIO & SELECTED WORK</span>
                    <h2 class="cletus-section-heading">Featured <span class="cletus-text-gold">Projects</span></h2>
                    <p class="cletus-section-lead">Explore selected software systems, web platforms, and creative design collections.</p>
                </div>

                <div class="cletus-projects-editorial-showcase">
                    <!-- Project 01: StockConnect (Real Asset PNG Connected) -->
                    <div class="cletus-showcase-row cletus-stagger-left">
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['stockconnect']['image_url']); ?>" 
                                 alt="StockConnect Web Application" 
                                 class="cletus-showcase-img" 
                                 id="stockconnect-project-img">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['stockconnect']['badge']); ?></span>
                        </div>
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">01</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['stockconnect']['category']); ?></span>
                            <h3 class="cletus-showcase-title"><?php echo esc_html($projects['stockconnect']['title']); ?></h3>
                            <p class="cletus-showcase-desc"><?php echo esc_html($projects['stockconnect']['desc']); ?></p>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['stockconnect']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?php echo esc_url(home_url('/portfolio-projects/')); ?>" class="cletus-btn cletus-btn-outline cletus-btn-sm">
                                <span><?php echo esc_html($projects['stockconnect']['action_text']); ?></span>
                                <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                            </a>
                        </div>
                    </div>

                    <!-- Project 02: Vecora Technologies (Real Asset PNG Connected) -->
                    <div class="cletus-showcase-row cletus-stagger-right">
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">02</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['vecora']['category']); ?></span>
                            <h3 class="cletus-showcase-title"><?php echo esc_html($projects['vecora']['title']); ?></h3>
                            <p class="cletus-showcase-desc"><?php echo esc_html($projects['vecora']['desc']); ?></p>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['vecora']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?php echo esc_url(home_url('/portfolio-projects/')); ?>" class="cletus-btn cletus-btn-outline cletus-btn-sm">
                                <span><?php echo esc_html($projects['vecora']['action_text']); ?></span>
                                <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                            </a>
                        </div>
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['vecora']['image_url']); ?>" 
                                 alt="Vecora Technologies Platform" 
                                 class="cletus-showcase-img" 
                                 id="vecora-project-img">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['vecora']['badge']); ?></span>
                        </div>
                    </div>

                    <!-- Project 03: Creative Designs (12-Image Continuous Horizontal Moving Reel) -->
                    <?php echo cletus_render_creative_designs_showcase(); ?>
                </div>

                <div class="text-center mt-40">
                    <a href="<?php echo esc_url(home_url('/portfolio-projects/#ideas')); ?>" class="cletus-btn cletus-btn-primary">
                        <span>Explore My Ideas</span>
                        <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                    </a>
                </div>

                <!-- Testimonial Section Header (Positioned Directly After Creative Designs) -->
                <div class="cletus-editorial-header cletus-testimonial-header mt-50">
                    <span class="cletus-section-kicker">04 / TESTIMONIALS & REVIEWS</span>
                    <h2 class="cletus-section-heading">Peer <span class="cletus-text-gold">Recommendation</span></h2>
                </div>

                <!-- Single Testimonial Card Section -->
                <div class="cletus-testimonial-card-single">
                    <div class="cletus-testimonial-quote-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M10 11H6C4.89543 11 4 10.1046 4 9V7C4 5.89543 4.89543 5 6 5H8C9.10457 5 10 5.89543 10 7V11ZM10 11V14C10 16.2091 8.20914 18 6 18H5" stroke="#D4AF37" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M20 11H16C14.8954 11 14 10.1046 14 9V7C14 5.89543 14.8954 5 16 5H18C19.1046 5 20 5.89543 20 7V11ZM20 11V14C20 16.2091 18.2091 18 16 18H15" stroke="#D4AF37" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <p class="cletus-testimonial-quote-text">
                        "I just checked out the platform and it blew me away. The design and functionality make it look completely legitimate—like a real, production-ready corporate stock platform. The integrated rating and review system makes the whole experience feel complete and professional. Exceptional execution!"
                    </p>
                    <div class="cletus-testimonial-author-meta">
                        <span class="cletus-testimonial-author-name">Enwelem Princess Chidera</span>
                        <span class="cletus-testimonial-author-title">Coursemate &bull; StockConnect Platform</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA Callout Banner -->
        <section class="cletus-section cletus-cta-banner">
            <div class="cletus-container text-center">
                <h2 class="cletus-cta-title">Have a Project or Creative Collaboration in Mind?</h2>
                <p class="cletus-cta-text">Let's collaborate to transform your concept into a high-performance web platform or stunning creative visual design.</p>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>" class="cletus-btn cletus-btn-primary">
                    <span>Start a Conversation</span>
                    <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                </a>
            </div>
        </section>
    </main>
    <?php
    cletus_render_footer();
    return ob_get_clean();
});

// 2. ABOUT PAGE SHORTCODE [cletus_about]
add_shortcode('cletus_about', function() {
    ob_start();
    cletus_render_header();
    $profile_image_url = CLETUS_PORTFOLIO_URL . 'assets/images/cletus-image.jpg';
    ?>
    <main class="cletus-main-content">
        <section class="cletus-page-header">
            <div class="cletus-container">
                <span class="cletus-section-kicker">BIOGRAPHY & BACKGROUND</span>
                <h1 class="cletus-page-title">About <span class="cletus-text-gold">Amos Egwolo Cletus</span></h1>
                <p class="cletus-page-lead">Computer Science Student at SDU, Creative Designer, & Web Developer</p>
            </div>
        </section>

        <section class="cletus-section">
            <div class="cletus-container">
                <div class="cletus-about-editorial-grid">
                    <div class="cletus-about-img-col">
                        <div class="cletus-profile-frame-square">
                            <div class="cletus-square-img-wrapper">
                                <img src="<?php echo esc_url($profile_image_url); ?>" alt="Amos Egwolo Cletus Photograph" class="cletus-profile-square-img" id="cletus-about-profile-img">
                                <div class="cletus-square-corner-accent top-left"></div>
                                <div class="cletus-square-corner-accent bottom-right"></div>
                            </div>
                        </div>
                    </div>

                    <div class="cletus-about-text-col">
                        <h2 class="cletus-subheading">Passionate About Technology & Visual Excellence</h2>
                        <p class="cletus-paragraph-lead">
                            Hello! I'm <strong>Amos Egwolo Cletus</strong>, a Computer Science student at SDU, a creative designer, and a full-stack web developer. My work is driven by a passion for building robust software systems while crafting elegant visual designs.
                        </p>
                        <p>
                            Whether engineering web applications like <em>StockConnect</em>, developing business technology platforms like <em>Vecora Technologies</em>, or crafting promotional graphics and visual branding in my <em>Creative Designs</em> showcase, I combine technical discipline with creative aesthetics.
                        </p>

                        <div class="cletus-editorial-list mt-30">
                            <div class="cletus-editorial-item-row">
                                <div class="cletus-editorial-item-icon">
                                    <?php echo cletus_get_icon_svg('education', 'cletus-bio-icon-svg'); ?>
                                </div>
                                <div>
                                    <h4>Education & Academic Foundation</h4>
                                    <p>Computer Science — SDU (Focus on Software Engineering & Web Systems)</p>
                                </div>
                            </div>

                            <div class="cletus-editorial-item-row">
                                <div class="cletus-editorial-item-icon">
                                    <?php echo cletus_get_icon_svg('code', 'cletus-bio-icon-svg'); ?>
                                </div>
                                <div>
                                    <h4>Technical Stack & Tools</h4>
                                    <p>HTML, CSS, JavaScript, Laravel, WordPress, PHP, MariaDB, Photoshop, Canva, Branding</p>
                                </div>
                            </div>
                        </div>

                        <div class="cletus-resume-action mt-40">
                            <a href="#" class="cletus-btn cletus-btn-primary" onclick="alert('Resume PDF download will be available when uploaded.'); return false;">
                                <?php echo cletus_get_icon_svg('resume', 'cletus-btn-icon-inline'); ?>
                                <span>Download Resume (CV)</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    cletus_render_footer();
    return ob_get_clean();
});

// 3. SERVICES PAGE SHORTCODE [cletus_services_page]
add_shortcode('cletus_services_page', function() {
    ob_start();
    cletus_render_header();
    ?>
    <main class="cletus-main-content">
        <section class="cletus-page-header">
            <div class="cletus-container">
                <span class="cletus-section-kicker">WHAT I OFFER</span>
                <h1 class="cletus-page-title">Services & <span class="cletus-text-gold">Expertise</span></h1>
                <p class="cletus-page-lead">Web application development, creative graphic design, and software engineering capabilities.</p>
            </div>
        </section>

        <section class="cletus-section">
            <div class="cletus-container">
                <div class="cletus-services-editorial-list">
                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">01</div>
                        <div class="cletus-service-row-body">
                            <h3>Creative Graphic Design</h3>
                            <p>Flyers, posters, promotional designs, branding, and visual graphics designed to give your project a professional edge.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('design', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">02</div>
                        <div class="cletus-service-row-body">
                            <h3>Full-Stack Web Development</h3>
                            <p>Building scalable, clean, and well-structured web applications using HTML5, CSS3, JavaScript, Laravel, and PHP.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('code', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">03</div>
                        <div class="cletus-service-row-body">
                            <h3>Frontend Development</h3>
                            <p>Interactive web interfaces built with smooth micro-animations, dark-mode themes, responsive grid systems, and fast execution.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('layout', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">04</div>
                        <div class="cletus-service-row-body">
                            <h3>UI & Visual Design Architecture</h3>
                            <p>Designing clean layouts, modern UI components, and brand identity materials that deliver great user experience.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('layers', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">05</div>
                        <div class="cletus-service-row-body">
                            <h3>WordPress & MariaDB Systems</h3>
                            <p>WordPress development, MariaDB database setup, custom theme styling, and Elementor page integrations.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('wordpress', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>

                    <div class="cletus-service-row">
                        <div class="cletus-service-row-num">06</div>
                        <div class="cletus-service-row-body">
                            <h3>Responsive Web Optimization</h3>
                            <p>Ensuring 100% responsiveness and fluid layout scaling across desktop computers, laptops, tablets, and mobile smartphones.</p>
                        </div>
                        <div class="cletus-service-row-icon">
                            <?php echo cletus_get_icon_svg('server', 'cletus-service-row-svg'); ?>
                        </div>
                    </div>
                </div>

                <!-- How My Services Can Be Useful to Others Section -->
                <div class="cletus-editorial-header mt-50">
                    <span class="cletus-section-kicker">VALUE & IMPACT</span>
                    <h2 class="cletus-section-heading">How My Services Can Be <span class="cletus-text-gold">Useful to Others</span></h2>
                </div>

                <div class="cletus-usefulness-grid">
                    <div class="cletus-usefulness-card">
                        <div class="cletus-usefulness-header">
                            <span class="cletus-usefulness-target">For Clients & Business Owners</span>
                        </div>
                        <p class="cletus-usefulness-desc">
                            Helps establish a powerful digital presence, automate business workflows, and present a modern visual identity that attracts customers and drives growth.
                        </p>
                    </div>

                    <div class="cletus-usefulness-card">
                        <div class="cletus-usefulness-header">
                            <span class="cletus-usefulness-target">For Students & Academic Communities</span>
                        </div>
                        <p class="cletus-usefulness-desc">
                            Provides tailored digital platforms, academic project collaboration tools, and custom portfolios that showcase technical skills and unlock new opportunities.
                        </p>
                    </div>

                    <div class="cletus-usefulness-card">
                        <div class="cletus-usefulness-header">
                            <span class="cletus-usefulness-target">For Organizations & Enterprises</span>
                        </div>
                        <p class="cletus-usefulness-desc">
                            Delivers reliable, custom WordPress platforms and scalable backend architectures engineered for high security, fast performance, and seamless user engagement.
                        </p>
                    </div>

                    <div class="cletus-usefulness-card">
                        <div class="cletus-usefulness-header">
                            <span class="cletus-usefulness-target">For Brands & Creative Projects</span>
                        </div>
                        <p class="cletus-usefulness-desc">
                            Transforms raw concepts into striking graphic designs, promotional artwork, and intuitive UI layouts that build strong visual recognition.
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    cletus_render_footer();
    return ob_get_clean();
});

// 4. PORTFOLIO PAGE SHORTCODE [cletus_portfolio_page]
add_shortcode('cletus_portfolio_page', function() {
    ob_start();
    cletus_render_header();
    $projects = cletus_get_portfolio_projects();
    ?>
    <main class="cletus-main-content">
        <section class="cletus-page-header">
            <div class="cletus-container">
                <span class="cletus-section-kicker">MY WORK & INNOVATION</span>
                <h1 class="cletus-page-title">Project & <span class="cletus-text-gold">Ideas</span></h1>
                <p class="cletus-page-lead">A showcase of web applications, WordPress systems, creative design work, and future project concepts.</p>
            </div>
        </section>

        <section class="cletus-section">
            <div class="cletus-container">
                <div class="cletus-projects-editorial-showcase">
                    <!-- Project 01: StockConnect (Real Asset PNG) -->
                    <div class="cletus-showcase-row cletus-stagger-left">
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['stockconnect']['image_url']); ?>" 
                                 alt="StockConnect Web Application" 
                                 class="cletus-showcase-img">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['stockconnect']['badge']); ?></span>
                        </div>
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">01</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['stockconnect']['category']); ?></span>
                            <h3 class="cletus-showcase-title"><?php echo esc_html($projects['stockconnect']['title']); ?></h3>
                            <p class="cletus-showcase-desc"><?php echo esc_html($projects['stockconnect']['desc']); ?></p>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['stockconnect']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Project 02: Vecora Technologies (Real Asset PNG) -->
                    <div class="cletus-showcase-row cletus-stagger-right">
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">02</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['vecora']['category']); ?></span>
                            <h3 class="cletus-showcase-title"><?php echo esc_html($projects['vecora']['title']); ?></h3>
                            <p class="cletus-showcase-desc"><?php echo esc_html($projects['vecora']['desc']); ?></p>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['vecora']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['vecora']['image_url']); ?>" 
                                 alt="Vecora Technologies Platform" 
                                 class="cletus-showcase-img">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['vecora']['badge']); ?></span>
                        </div>
                    </div>

                    <!-- Project 03: Creative Designs (12-Image Continuous Horizontal Moving Reel) -->
                    <?php echo cletus_render_creative_designs_showcase(false); ?>
                </div>

                <!-- Ideas for Future Projects Section -->
                <div class="cletus-editorial-header mt-50" id="ideas">
                    <span class="cletus-section-kicker">04 / CONCEPTUAL INNOVATION & FUTURE ROADMAP</span>
                    <h2 class="cletus-section-heading">Projects / <span class="cletus-text-gold">Ideas</span></h2>
                    <p class="cletus-section-lead">Conceptual web platforms and software application ideas designed for practical real-world impact.</p>
                </div>

                <div class="cletus-projects-editorial-showcase cletus-ideas-showcase">
                    <!-- Idea 01: StudentHub -->
                    <div class="cletus-showcase-row cletus-stagger-left">
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['studenthub']['image_url']); ?>" 
                                 alt="StudentHub Platform Concept" 
                                 class="cletus-showcase-img"
                                 loading="lazy">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['studenthub']['badge']); ?></span>
                        </div>
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">01</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['studenthub']['category']); ?></span>
                            <h3 class="cletus-showcase-title">Idea 01 — <?php echo esc_html($projects['studenthub']['title']); ?></h3>
                            <div class="cletus-project-details">
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">PROBLEM</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['studenthub']['problem']); ?></p>
                                </div>
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">SOLUTION</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['studenthub']['solution']); ?></p>
                                </div>
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">WHAT'S NEXT</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['studenthub']['whats_next']); ?></p>
                                </div>
                            </div>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['studenthub']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Idea 02: Local Business Digital Hub -->
                    <div class="cletus-showcase-row cletus-stagger-right">
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">02</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['local_business_hub']['category']); ?></span>
                            <h3 class="cletus-showcase-title">Idea 02 — <?php echo esc_html($projects['local_business_hub']['title']); ?></h3>
                            <div class="cletus-project-details">
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">PROBLEM</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['local_business_hub']['problem']); ?></p>
                                </div>
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">SOLUTION</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['local_business_hub']['solution']); ?></p>
                                </div>
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">WHAT'S NEXT</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['local_business_hub']['whats_next']); ?></p>
                                </div>
                            </div>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['local_business_hub']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['local_business_hub']['image_url']); ?>" 
                                 alt="Local Business Digital Hub Concept" 
                                 class="cletus-showcase-img"
                                 loading="lazy">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['local_business_hub']['badge']); ?></span>
                        </div>
                    </div>

                    <!-- Idea 03: SkillBridge -->
                    <div class="cletus-showcase-row cletus-stagger-left">
                        <div class="cletus-showcase-img-box">
                            <img src="<?php echo esc_url($projects['skillbridge']['image_url']); ?>" 
                                 alt="SkillBridge Platform Concept" 
                                 class="cletus-showcase-img"
                                 loading="lazy">
                            <span class="cletus-showcase-badge"><?php echo esc_html($projects['skillbridge']['badge']); ?></span>
                        </div>
                        <div class="cletus-showcase-text-box">
                            <span class="cletus-showcase-num">03</span>
                            <span class="cletus-showcase-cat"><?php echo esc_html($projects['skillbridge']['category']); ?></span>
                            <h3 class="cletus-showcase-title">Idea 03 — <?php echo esc_html($projects['skillbridge']['title']); ?></h3>
                            <div class="cletus-project-details">
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">PROBLEM</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['skillbridge']['problem']); ?></p>
                                </div>
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">SOLUTION</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['skillbridge']['solution']); ?></p>
                                </div>
                                <div class="cletus-project-block">
                                    <h4 class="cletus-project-label">WHAT'S NEXT</h4>
                                    <p class="cletus-showcase-desc"><?php echo esc_html($projects['skillbridge']['whats_next']); ?></p>
                                </div>
                            </div>
                            <div class="cletus-showcase-tech">
                                <?php foreach ($projects['skillbridge']['tags'] as $tag) : ?>
                                    <span><?php echo esc_html($tag); ?></span>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    cletus_render_footer();
    return ob_get_clean();
});

// 5. BLOG PAGE SHORTCODE [cletus_blog_page] (Dynamic WordPress Posts Engine)
add_shortcode('cletus_blog_page', function() {
    ob_start();
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
    return ob_get_clean();
});

// Helper: Get Post Featured Image URL
function cletus_get_post_featured_image_url($post_id) {
    $custom_url = get_post_meta($post_id, '_cletus_featured_image_url', true);
    if (!empty($custom_url)) {
        return $custom_url;
    }
    if (has_post_thumbnail($post_id)) {
        return get_the_post_thumbnail_url($post_id, 'full');
    }
    return false;
}

// Remove Astra theme header/footer actions on portfolio blog pages
add_action('template_redirect', function() {
    if (is_home() || is_page('blog') || is_page(16) || is_single() || is_category()) {
        remove_all_actions('astra_header');
        remove_all_actions('astra_footer');
        remove_all_actions('astra_site_header');
        remove_all_actions('astra_site_footer');
        remove_all_actions('astra_entry_header');
        remove_all_actions('astra_entry_footer');
    }
}, 1);

// Single Post & Blog Index Template Filter for WordPress Articles (Priority 99999)
add_filter('template_include', function($template) {
    if (is_single()) {
        $post_id = get_the_ID();
        if ($post_id && get_post_type($post_id) === 'post') {
            $custom_template = CLETUS_PORTFOLIO_PATH . 'templates/single-post.php';
            if (file_exists($custom_template)) {
                return $custom_template;
            }
        }
    }

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (is_home() || is_page('blog') || is_page(16) || is_category() || is_tag() || strpos($request_uri, '/blog') !== false) {
        $custom_blog_template = CLETUS_PORTFOLIO_PATH . 'templates/blog-index.php';
        if (file_exists($custom_blog_template)) {
            return $custom_blog_template;
        }
    }

    return $template;
}, 99999);

// 6. CONTACT PAGE SHORTCODE [cletus_contact_page]
add_shortcode('cletus_contact_page', function() {
    ob_start();
    cletus_render_header();
    ?>
    <main class="cletus-main-content">
        <section class="cletus-page-header">
            <div class="cletus-container">
                <span class="cletus-section-kicker">GET IN TOUCH</span>
                <h1 class="cletus-page-title">Let's <span class="cletus-text-gold">Connect</span></h1>
                <p class="cletus-page-lead">Have a web project, creative design request, or technical collaboration in mind? Reach out today.</p>
            </div>
        </section>

        <section class="cletus-section">
            <div class="cletus-container">
                <div class="cletus-contact-editorial-grid">
                    <div class="cletus-contact-info-col">
                        <h2 class="cletus-subheading">Contact & Collaboration</h2>
                        <p class="cletus-paragraph-lead">Feel free to send a message. I'm always open to discussing web applications, creative design projects, or technical opportunities.</p>

                        <div class="cletus-contact-list mt-30">
                            <div class="cletus-contact-item-row">
                                <div class="cletus-contact-item-icon">
                                    <?php echo cletus_get_icon_svg('mail', 'cletus-contact-icon-svg'); ?>
                                </div>
                                <div>
                                    <h4>Email Address</h4>
                                    <p><a href="mailto:amoscletus22@gmail.com">amoscletus22@gmail.com</a></p>
                                </div>
                            </div>

                            <div class="cletus-contact-item-row">
                                <div class="cletus-contact-item-icon">
                                    <?php echo cletus_get_icon_svg('location', 'cletus-contact-icon-svg'); ?>
                                </div>
                                <div>
                                    <h4>Location & Academic Hub</h4>
                                    <p>SDU Campus / Local Development Hub</p>
                                </div>
                            </div>

                            <div class="cletus-contact-item-row">
                                <div class="cletus-contact-item-icon">
                                    <?php echo cletus_get_icon_svg('whatsapp', 'cletus-contact-icon-svg'); ?>
                                </div>
                                <div>
                                    <h4>WhatsApp Business</h4>
                                    <p><a href="https://wa.me/message/7WBKWEIFFIZQP1" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp Business">Chat on WhatsApp Business</a></p>
                                </div>
                            </div>

                            <div class="cletus-contact-item-row">
                                <div class="cletus-contact-item-icon">
                                    <?php echo cletus_get_icon_svg('status', 'cletus-contact-icon-svg'); ?>
                                </div>
                                <div>
                                    <h4>Availability</h4>
                                    <p class="cletus-text-gold">Available for Select Web & Design Projects</p>
                                </div>
                            </div>
                        </div>

                        <div class="cletus-contact-socials-box mt-40">
                            <h4 class="cletus-socials-title">Follow & Connect</h4>
                            <div class="cletus-social-pills-row">
                                <?php foreach (cletus_get_social_links() as $key => $social) : ?>
                                    <a href="<?php echo esc_url($social['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr($social['label']); ?>">
                                        <?php echo cletus_get_icon_svg($social['icon']); ?>
                                        <span><?php echo esc_html($social['name']); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="cletus-contact-form-col">
                        <h2 class="cletus-subheading">Send a Message</h2>
                        <form class="cletus-custom-form" action="#" method="post" onsubmit="alert('Thank you! Your message has been sent successfully.'); return false;">
                            <div class="cletus-form-group">
                                <label for="form-name">Your Name</label>
                                <input type="text" id="form-name" name="name" placeholder="Enter your name" required>
                            </div>
                            <div class="cletus-form-group">
                                <label for="form-email">Your Email</label>
                                <input type="email" id="form-email" name="email" placeholder="Enter your email address" required>
                            </div>
                            <div class="cletus-form-group">
                                <label for="form-subject">Subject</label>
                                <input type="text" id="form-subject" name="subject" placeholder="Project Inquiry / Creative Design Request" required>
                            </div>
                            <div class="cletus-form-group">
                                <label for="form-message">Message</label>
                                <textarea id="form-message" name="message" rows="5" placeholder="Tell me about your project or idea..." required></textarea>
                            </div>
                            <button type="submit" class="cletus-btn cletus-btn-primary cletus-btn-block">
                                <span>Send Message</span>
                                <?php echo cletus_get_icon_svg('arrow', 'cletus-btn-icon-inline'); ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <?php
    cletus_render_footer();
    return ob_get_clean();
});
