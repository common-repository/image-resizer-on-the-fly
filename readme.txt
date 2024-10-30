=== Image Resizer On The Fly ===
Contributors: wework4web
Donate link: http://wework4web.com
Tags: image, featured image, image resizer
Requires at least: 3.0.
Tested up to: 3.3.2
Stable tag: 1.1

This plugin resize featured image on the fly and gives u ability to remove automatically resized image from admin.

== Description ==
With the help of the shortcode, this plugin helps you to resize your image in different size. You never need to update your thumbnail size settings and re-upload all the image as you used to do before. It gives you the unlimited limitation for image resize.

<strong>How To Use</strong>

[image-resize width=100 height=100 ref=w default=your_default_image.png]

Parameters:
<strong>width:</strong>   (required) Width of the image in px

<strong>height:</strong>  (required) Height of the image in px

<strong>Ref:</strong>	  (optional) w if you want the width reference, h for height reference. No ref parameter means you will have image with maintained ratio.

<strong>default:</strong> (optional) Your default image path. The default image is shown when you don't have featured image in your post.

You can use the shortcode in your post,page content area.

For using in templates, you can use the wordpress do_shortcode function. Like:

&lt;?php do_shortcode('[image-resize width=100 height=100 ref=w default=your_default_image.png]'); ?&gt;

While using in templates, it must be inside the post loop.

If you have any queries do write in the support section.

== Installation ==

1. Upload the plugin Image Resizer On The Fly files to the `/wp-content/plugins/` directory
1. Activate the Image Resizer On The Fly plugin through the 'Plugins' menu in WordPress.
1. Use shortcode in your template, post, pages.

== Frequently Asked Questions ==

= Does it saves the image size for future reference ? =

Yes.

== Screenshots ==

1. Your resized images list in admin.

== Changelog ==

= 1.1 =
* Fixed Delete Link.

= 1.0 =
* First Release