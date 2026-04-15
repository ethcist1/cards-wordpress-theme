<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> | <?php bloginfo('description'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <a class="skip-link screen-reader-text" href="#main">
        <?php esc_html_e('Skip to content', 'sparks-theme'); ?>
    </a>
    <header class="<?php echo is_front_page() ? 'header-transparent' : ''; ?>">
        <div class="header-inner">
            <div class="header-logo">
                <?php
                if (has_custom_logo()) {
                    the_custom_logo();
                } else {
                    echo '<h1><a href="' . esc_url(home_url('/')) . '">' . esc_html(get_bloginfo('name')) . '</a></h1>';
                    echo '<p>' . esc_html(get_bloginfo('description')) . '</p>';
                }
                ?>
            </div>
            <nav>
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container'      => '',
                    'menu_class'     => 'header-menu',
                ));
                ?>
            </nav>
        </div>
    </header>
