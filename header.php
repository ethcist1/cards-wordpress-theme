<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php bloginfo('name'); ?> | <?php bloginfo('description'); ?></title>
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
    <header>
<!--          logo -->
		
		<div class="header-logo">
        <?php
        if (has_custom_logo()) {
            the_custom_logo();
        } else {
            echo '<h1><a href="' . home_url() . '">' . get_bloginfo('name') . '</a></h1>';
            echo '<p>' . get_bloginfo('description') . '</p>';
        }
        ?>
    </div>
		
<!-- 		navigation -->
        <nav>
            <?php
            wp_nav_menu(array(
                'theme_location' => 'primary',
                'container'      => '',
                'menu_class'     => 'header-menu',
                // Additional parameters can be specified here
            ));
            ?>
        </nav>
    </header>
