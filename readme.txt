=== XML Nest Creator ===
Contributors: gunjanjaswal
Tags: sitemap, xml, seo, rankmath, yoast
Requires at least: 5.5
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A simple XML creator for posts, pages, categories (or others) that generates a sitemap.xml and smartly overrides popular SEO plugins.

== Description ==

Generating a standard `sitemap.xml` shouldn't be overly complex or bloated. Many of the most popular SEO plugins provide a sitemap, but they can be rigid or packed with unused functionality. **XML Nest Creator** specializes in exactly one thing: dynamically generating a valid, highly efficient `sitemap.xml` file, while intelligently bypassing duplicate functionality from popular SEO plugins.

= Key Features =

* **Surgical Precision:** Select exactly which Post Types (Posts, Pages, Custom) and Taxonomies (Categories, Tags) you want indexed.
* **Conflict Free Core:** Automatically overrides and disables the native WordPress sitemap (`wp-sitemap.xml`) to prevent duplicate mapping.
* **SEO Plugin Compatible:** Automatically disables the sitemap functionalities of **Yoast SEO** and **Rank Math**, while maintaining all their other powerful SEO and schema markup features under the hood. 
* **Dynamic Rendering:** Instantly regenerates the `sitemap.xml` payload upon request; no cron jobs or static file management required.
* **Native UI:** Uses the seamless, fast native WordPress Settings API—no bloated custom dashboards!

== Installation ==

1. Upload the `xml-nest-creator` folder to the `/wp-content/plugins/` directory, or install via the WordPress Plugin Directory.
2. Activate the plugin through the 'Plugins' menu in WordPress. *(This automatically flushes rewrite rules!)*
3. Navigate to **Settings > XML Nest Creator** to select the post types and taxonomies you want to include in your sitemap.
4. Visit `yourwebsite.com/sitemap.xml` to see your generated sitemap live.

== Frequently Asked Questions ==

= Does it break Yoast SEO or Rank Math? =
No! It intelligently disables *only* their XML sitemap generation modules via official plugin hooks (`wpseo_xml_sitemap_enable` and `rank_math/sitemap/enable`). You keep their meta tags and schema markup without duplicate sitemaps.

= Does it support Custom Post Types? =
Yes! The settings page automatically queries your WordPress database and lists all custom post types and custom taxonomies that are currently registered as "public" (like WooCommerce products or Portfolios). 

= Where can I submit a bug or feature request? =
You can reach out to Gunjan Jaswal via [hello@gunjanjaswal.me](mailto:hello@gunjanjaswal.me) or visit [gunjanjaswal.me](https://www.gunjanjaswal.me).

== Screenshots ==

1. The XML Nest Creator Settings Page where you dictate indexation rules.

== Changelog ==

= 1.0.0 =
* Initial release. Features dynamic `sitemap.xml` rewrite rule generation.
* Added native WP Settings API UI for post type and taxonomy selection.
* Added conflict overrides for Yoast SEO, Rank Math, and native WP Core sitemaps.
