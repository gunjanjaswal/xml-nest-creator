=== XML Nest Creator ===
Contributors: gunjanjaswal
Tags: simple xml generator, simple sitemap, xml generator, sitemap, seo
Requires at least: 5.5
Tested up to: 6.5
Stable tag: 1.0.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A very simple XML generator for WordPress. Create a clean sitemap.xml file for your posts, pages, and categories without any bloated settings.

== Description ==

Looking for a **simple XML generator**? XML Nest Creator is the easiest way to generate a `sitemap.xml` file for your WordPress website. Many SEO plugins come with too many complicated settings. This simple sitemap generator does one thing perfectly: it creates a clean XML sitemap so search engines can easily find your content.

= Key Features =

* **Simple to Use:** Just check a box to choose which Posts, Pages, or Categories to include in your sitemap.
* **No Conflicts:** It safely turns off the default WordPress sitemap so you don't have duplicates.
* **Works with SEO Plugins:** Automatically disables the sitemap feature of Yoast SEO and Rank Math, while letting you keep all their other helpful SEO features.
* **Always Up to Date:** Instantly builds your `sitemap.xml` when a search engine asks for it. No cron jobs or messy files.
* **Clean Settings Page:** A fast, simple settings page built right into WordPress.

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
