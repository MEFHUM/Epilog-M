=== Bir Misra ===
Contributors: Samet Altun
Link: http://github.com/sametaltun
Requires at least: 2.8
Tested up to: 4.7

Insert quotes -in my case, verses- and pull them randomly into your pages and posts (via shortcodes) or your template (via template tags).

== Description ==

Insert quotes and pull them randomly into your pages and posts (via shortcodes) or your template (via template tags).
Can refer to quote IDs to use specific quotes. Also widget-enabled

== Installation ==

1. Upload the contents of the zip file to the your plugins directory (default: `/wp-content/plugins/`)
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Go to the Bir Misra page under Settings
1. Add/edit the quotes you'd like to use on your site
1. To display in a page or post, use the short code: [erq], or [erq id={id}] if you'd like to use only a specific quote
1. To add to your template, use the template tag: `<?php echo erq_shortcode(); ?>`, or `<?php echo erq_shortcode(array('id' => '{id}')); ?>`  if you'd like to use only a specific quote