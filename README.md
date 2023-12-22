# WP-Acknowledge-Now
=== WP Acknowledge Me ===

This Wordpress Plugin will redirect logged-in users to an acknowledgment page and record their acknowledgment status.
Logged-in users will be redirected to the acknowledgment page only once, where they can acknowledge or decline.
The acknowledgment status will be recorded and displayed in the Users section of the WordPress admin.
Administrators can manually change the acknowledgment status in the user profile editing page.
If the user clicks "Yes, I acknowledge," they will be redirected to the home page, and if they click "No, I don't" and have the "Not Verified" role, they will be redirected to their profile; otherwise, they will be redirected to google.com.

---
*You can change the "Not Verified" role to another role where the user doesn't have as much access.*

---
**This section describes how to install the plugin and get it working.**
1. Make a folder in the `/wp-content/plugins/` directory
1. Upload `WP-Acknowledge-Me.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
