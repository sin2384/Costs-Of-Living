=== Plugin Name ===
Contributors: sin2384
Donate link: http://www.themesbros.com
Tags: pricing, travel, costs, submission
Requires at least: 4.0
Tested up to: 4.9.4
Stable tag: 4.9.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Display table of prices for the user defined items or services per post basis.

== Description ==

This plugin is perfect for digital nomads, or travel sites. The idea behind the plugin is to copy the functionality
of numbeo.com which displays costs of living in the certain city. However, it can be used for other purposes too, such as
displaying repetitive data / prices per post.

How it's working?

First, you define a categories (for example "Market" and "Transport"), and then you add items to it.
For example, you could add eggs or milk to the Market category, and taxi or a bus to the Transport category.
Then you go to new or existing post, and enter the price for each item (eggs, milk, taxi, bus), or not.
You can also choose not to show certain categories on certain posts if those categories are not required.
Prices will be rendered in a table on frontend.

You can also allow your visitors to submit the prices for the items you created, and to moderate those submissions.
If submission is approved, plugin will calculate the average price for the item, and display it instead of the original
price you entered.

How to display table of prices?

After you create categories, items and enter the prices, you need to add a shortcode `[cofl]` to that post. That's it.

How to allow user submissions?
Create a page "User submissions" for example and save it. Go to plugin settings page, and select that page under "Please select the prices submit page."


== Installation ==

1. Upload `cofl.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==

= 1.0 =
Initial version.


