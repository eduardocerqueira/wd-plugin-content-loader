# wd-plugin-content-loader

wordpress plugin for multi-language content loader. This plugin assumes you have full control over the content structure and provides a simple mechanism to load multilingual content dynamically.

## Install

1. upload `multilingual-content-loader.php` in the /wp-content/plugins/ directory.
2. Activate the plugin in the WordPress admin dashboard.

![screenshot](/img/screenshot.png)

3. After activating the plugin, the table `wp_site_content` will be created automatically.
Use a database management tool like phpMyAdmin to insert your language-specific content into the table.

```
Example row: language = 'en', content = '<h1>Welcome to My Site</h1>'.
```

![table](/img/table.png)


## Test the Plugin

Visit your site, select a language from the dropdown, and the page will reload with the corresponding content.

