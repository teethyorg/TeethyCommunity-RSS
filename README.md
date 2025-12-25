# TeethyCommunity RSS for FluentCommunity

A specialized WordPress plugin that generates a high-compatibility RSS feed for **FluentCommunity** public posts. This plugin is designed specifically to ensure community updates look professional when shared on social media platforms like Facebook and X (Twitter).

## 🌐 Live Demo
Check out the live feed in action:  
[https://teethy.org/?teethycommunity_rss=1](https://teethy.org/?teethycommunity_rss=1)

## 🚀 Features

- **Public-Only Feed:** Strictly respects privacy settings, only exposing posts marked as "Public" and "Published."
- **Social Media Optimized:** - Generates a clean, plain-text `<description>` snippet for perfect link previews.
    - Provides full HTML `<content:encoded>` for rich-text support in RSS readers.
- **Space Filtering:** Dynamically generate feeds for specific community "Spaces."
- **Correct Permalinks:** Deep-links directly to the post within its specific space (`/space/slug/post/slug`).
- **Lightweight:** Efficient SQL queries and optimized for performance.

## 🛠 Installation

1. Upload the plugin files to the `/wp-content/plugins/teethycommunity-rss` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Ensure **FluentCommunity** is installed and active.

## ⚙️ Technical Details

- **Requirements:** WordPress 5.0+, PHP 7.4+, FluentCommunity Plugin.
- **Hooks:** Uses `init` to intercept the request before the theme loads, reducing server overhead.
- **Database:** Directly queries the `fcom_posts` and `fcom_spaces` tables for maximum efficiency.
  
## 🔗 Usage

Access your feed by adding the `teethycommunity_rss` parameter to your URL:

| Feed Type | URL Example |
| :--- | :--- |
| **Global Feed** | `yourdomain.com/?teethycommunity_rss=1` |
| **Single Space** | `yourdomain.com/?teethycommunity_rss=1&space=slug` |
| **Multi-Space** | `yourdomain.com/?teethycommunity_rss=1&spaces=slug1,slug2` |

## ⚙️ Plugin Details

- **Version:** 1.4
- **Author:** [teethy](https://teethy.org)
- **Plugin URI:** [https://teethy.org](https://teethy.org)
- **License:** GPL v2 or later

---
*Developed for the community at [teethy.org](https://teethy.org)*
