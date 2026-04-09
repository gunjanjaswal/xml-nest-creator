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

Generating a standard `sitemap.xml` shouldn't be overly complex or bloated. Many popular SEO plugins provide a sitemap, but they can be rigid or packed with extra functionality. **XML Nest Creator** specializes in exactly one thing: dynamically generating a valid, highly efficient `sitemap.xml` file, while intelligently bypassing duplicate functionality from popular SEO plugins.

## 🚀 Key Features

* **🎯 Surgical Precision:** Decide exactly which **Post Types** (Posts, Pages, products, portfolio) and **Taxonomies** (Categories, Tags) you want indexed via an easy checkbox UI.
* **🛡️ Conflict-Free Core:** Automatically overrides and disables the native WordPress sitemap (`wp-sitemap.xml` introduced in WP 5.5) to prevent duplicate mapping.
* **🔌 SEO Plugin Compatible:** Automatically disables the sitemap functionalities of **Yoast SEO** and **Rank Math**, while natively preserving all their other powerful SEO and schema markup features under the hood. 
* **⚡ Dynamic Rendering:** Instantly regenerates the XML payload upon request via the native Rewrite API; no messy cron jobs or static file generation required.
* **🕹️ Native UI:** Utilizes the lightweight, fast WordPress Settings API—no bloated dashboards or aggressive upsells!

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
