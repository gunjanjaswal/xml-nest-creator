<div align="center">
  <h1>🗺️ XML Nest Creator</h1>
  <p><strong>A simple, highly customizable and conflict-free XML Sitemap Generator for WordPress.</strong></p>
  
  [![Version](https://img.shields.io/badge/version-1.0.0-blue.svg)](https://github.com/gunjanjaswal/xml-nest-creator)
  [![WordPress](https://img.shields.io/badge/WordPress-5.5+-green.svg)](https://wordpress.org)
  [![License](https://img.shields.io/badge/license-GPLv2-brightgreen.svg)](https://github.com/gunjanjaswal/xml-nest-creator)
  [![Author](https://img.shields.io/badge/author-Gunjan_Jaswal-orange.svg)](https://www.gunjanjaswal.me)
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

## 📄 License

This project is licensed under the GPL-2.0 License - see the LICENSE file for details.
