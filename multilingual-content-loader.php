<?php
/**
 * Plugin Name: Language Switcher Plugin
 * Description: A plugin to load site content based on selected language from the database.
 * Version: 1.0
 * Author: Your Name
 */

// Hook to add language dropdown to the WordPress menu.
add_action('wp_footer', 'language_switcher_dropdown');

// Hook to load the selected language content.
add_action('init', 'set_selected_language');

function language_switcher_dropdown() {
    // Get current language selection from session or default to 'en'.
    $current_language = isset($_SESSION['selected_language']) ? $_SESSION['selected_language'] : 'en';

    // Define available languages (match these to your database entries).
    $languages = [
        'en' => 'English',
        'pt' => 'Portuguese'
    ];

    // Render the dropdown menu.
    echo '<form id="language-switcher" method="POST" style="text-align: center; margin: 20px;">';
    echo '<select name="language" onchange="document.getElementById(\'language-switcher\').submit();">';

    foreach ($languages as $lang_code => $lang_name) {
        $selected = ($lang_code === $current_language) ? 'selected' : '';
        echo "<option value='$lang_code' $selected>$lang_name</option>";
    }

    echo '</select>';
    echo '</form>';
}

function set_selected_language() {
    // Start session if not already started.
    if (!session_id()) {
        session_start();
    }

    // Check if a new language is selected.
    if (isset($_POST['language'])) {
        $_SESSION['selected_language'] = sanitize_text_field($_POST['language']);

        // Reload page to show content in the selected language.
        wp_redirect($_SERVER['REQUEST_URI']);
        exit;
    }
}

// Hook to replace page content with the selected language content.
add_filter('the_content', 'load_content_by_language');

function load_content_by_language($content) {
    global $wpdb;

    // Get the current selected language.
    $current_language = isset($_SESSION['selected_language']) ? $_SESSION['selected_language'] : 'en';

    // Query the database for content in the selected language.
    $table_name = $wpdb->prefix . 'language_content'; // Assuming your table is named wp_language_content.
    $page_id = get_the_ID(); // Get the current page ID.

    $language_content = $wpdb->get_var($wpdb->prepare(
        "SELECT content FROM $table_name WHERE language = %s AND page_id = %d",
        $current_language,
        $page_id
    ));

    // Return the language-specific content if found, otherwise the default content.
    return $language_content ? $language_content : $content;
}

// Create the database table on plugin activation.
register_activation_hook(__FILE__, 'create_language_content_table');

function create_language_content_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'language_content'; // Table name.
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        page_id BIGINT(20) UNSIGNED NOT NULL,
        language VARCHAR(10) NOT NULL,
        content LONGTEXT NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
