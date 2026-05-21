<div align="center">
  <h1>🗺️ XML Nest Creator</h1>
  <p><strong>A simple, highly customizable and conflict-free XML Sitemap Generator for WordPress.</strong></p>
  
  [![Version](https://img.shields.io/badge/version-1.0.3-blue.svg)](https://github.com/gunjanjaswal/xml-nest-creator)
  [![WordPress](https://img.shields.io/badge/WordPress-5.5+-green.svg)](https://wordpress.org)
  [![License](https://img.shields.io/badge/license-GPLv2-brightgreen.svg)](https://github.com/gunjanjaswal/xml-nest-creator)
  [![Author](https://img.shields.io/badge/author-Gunjan_Jaswal-orange.svg)](https://www.gunjanjaswal.me)
  [![Support on Ko-fi](https://img.shields.io/badge/Ko--fi-Support-FF5E5B?logo=ko-fi&logoColor=white)](https://ko-fi.com/gunjanjaswal)
</div>

<hr>

## ✨ Why XML Nest Creator?

Looking for a **simple XML generator**? XML Nest Creator is the absolute easiest way to generate a `sitemap.xml` file for your WordPress website. Many SEO plugins come with too many complicated or confusing settings. This simple sitemap generator is designed to do exactly one thing perfectly: it creates a clean XML sitemap so that search engines can easily find and index your content.

## 🚀 Key Features

* **🎯 Simple to Use:** Use our easy checkboxes to decide exactly which **Post Types** (Posts, Pages, products) and **Taxonomies** (Categories, Tags) you want in your sitemap.
* **🛡️ No Conflicts:** It safely turns off the default WordPress sitemap (`wp-sitemap.xml`) so you don't have duplicates.
* **🔌 Works with SEO Plugins:** Automatically disables the sitemap feature of **Yoast SEO** and **Rank Math**, while letting you keep all their other helpful SEO features and meta tags. 
* **⚡ Always Up to Date:** Instantly builds your XML sitemap when a search engine asks for it. No messy cron jobs or files required.
* **🕹️ Clean Settings Page:** Enjoy a fast, simple settings page built right into WordPress without any bloated dashboards!

## 📥 Installation

1. Download the latest release from the repository or clone it into your WordPress installation: 
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/gunjanjaswal/xml-nest-creator.cl
   ```
2. Activate the plugin through the **Plugins** menu in WordPress. 
   *(Note: Activating automatically flushes your rewrite rules so `sitemap.xml` works instantly!)*
3. Navigate to **Settings > XML Nest Creator** in your dashboard.
4. Select the post types and taxonomies you want to include.
5. Hit save and visit `yoursite.com/sitemap.xml`!

## 📸 Usage & Configuration

Once activated, simply head over to the settings menu. The plugin automatically fetches all "Public" custom post types and taxonomies installed on your site. If you add WooCommerce or a Custom Post Type later, it will automatically populate as a checkbox!

## 🙋‍♂️ Support & Maintainer

Maintained and developed by **Gunjan Jaswal**.
* 🌍 Website: [www.gunjanjaswal.me](https://www.gunjanjaswal.me)
* 📧 Email: [hello@gunjanjaswal.me](mailto:hello@gunjanjaswal.me)
* ☕ Ko-fi: [ko-fi.com/gunjanjaswal](https://ko-fi.com/gunjanjaswal)

## 📝 Changelog

### 1.0.3
- Updated "Tested up to" to WordPress 7.0.
- Bumped minimum PHP requirement to 7.4 (WordPress 7.0 dropped support for PHP 7.2 and 7.3).
- Added `Settings` and `Support on Ko-fi` links to plugin action links (next to Deactivate).
- Added `Contact Developer` link to plugin row meta on the Plugins screen.
- Added `Requires at least`, `Tested up to`, and `Requires PHP` headers to the main plugin file.

### 1.0.2
- Renamed prefix from `xnc` to `xmlnc` (5 chars) to meet WordPress.org uniqueness requirement.

### 1.0.1
- Added License header to main plugin file.
- Added sanitization callback for `register_setting()`.
- Updated "Tested up to" to WordPress 6.9.

### 1.0.0
- Initial release. Dynamic `sitemap.xml` rewrite rule generation.
- Native WP Settings API UI for post type and taxonomy selection.
- Conflict overrides for Yoast SEO, Rank Math, and native WP Core sitemaps.

## 📄 License

This project is licensed under the GPL-2.0 License - see the LICENSE file for details.
