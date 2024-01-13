# Theme README

## Overview
Cards  Theme is a WordPress theme designed with a focus on showcasing posts with an option to display videos and images. It utilizes a modular approach with reusable components for efficient theme management and customization.

Post videos are displayed right in the main feed. If a post has multiple videos, they are paginated so readers can cycle through them.

## Theme Structure

### Templates
- **Archive Template (`archive.php`)**: Used for displaying post archives in a paginated format. Checks for videos or featured images in each post.
- **Testing Scroll Template (`testing-scroll.php`)**: A component-based template that uses `post-card.php` for displaying individual posts. Suitable for pages like `/testing-homepage`.

### Components
- **Post Card (`/elements/post-card.php`)**: A reusable component for displaying individual posts. Used in various templates for consistent post presentation.

### Functions
- **Theme Functions (`functions.php`)**: Enqueues styles and scripts required by the theme. Contains theme setup functions for features like custom logo, menus, widget areas, and featured images. Includes a function to extract the first video shortcode from post content. Defines custom pagination behavior.

### CSS/JS
- **Post Card Styles (`/elements/post-card.css`)**: Specific styles for the post card component.
- **Post Card Script (`/elements/post-card.js`)**: JavaScript functionality specific to the post card component.

### Additional Files
- **Header (`header.php`)**: Contains the HTML structure and PHP code for the theme's header.
- **Footer (`footer.php`)**: Contains the HTML structure and PHP code for the theme's footer.
- **Sidebar (`sidebar.php`)**: Defines the sidebar area and widgets.

## Usage
To use this theme, activate it from the WordPress dashboard. Customize it using the WordPress customizer and widgets interface.

## Customization
The theme can be customized by modifying the CSS and PHP files. Ensure to maintain a backup of the original files before making changes. 

For template-specific modifications, refer to the respective PHP files. CSS changes can be made in the theme's main stylesheet or the component-specific CSS files.

## Theme Update and Deployment

### Theme Update Mechanism
Sparks Theme includes an automated update mechanism, enabling easy updates across multiple sites. This system checks for new versions and allows users to update the theme directly from their WordPress dashboard.

- **Update JSON**: The theme periodically checks a JSON file hosted on a server for the latest version information and the URL to download the update package.
- **JSON Structure**: The file contains `version` and `download_url` fields. When a new version is released, update this file with the new version number and the URL to the updated theme's .zip file.

### Example JSON:

{
    "version": "1.1.0",
    "download_url": "http://sparksofanation.com/downloads/Sparks_Theme_v1.1.0.zip"
}

### Deployment Across Sites:
- Theme can be deployed across multiple sites, each can check and update independently.
- Ensure JSON file URL in functions.php is correct, and .zip file is updated with new theme versions.

### Important Notes:
- Update mechanism relies on a publicly accessible .zip file. 
