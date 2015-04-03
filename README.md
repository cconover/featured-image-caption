Featured Image Caption
======================
Featured Image Caption is a WordPress plugin that creates a custom post meta field and meta box to set a caption for the featured image of a post. This data is saved to the post meta, and can be added to your theme either automatically or using the provided function.

# Installation
1. Upload the `featured-image-caption` directory to your site's `plugins` directory.
2. Activate the plugin through the `Plugins` menu in WordPress.
3. Review the plugin options found in `Settings` -> `Featured Image Caption`.

# Usage and Options
**How do I set the caption for the featured image?**
The meta box is added in the side column of the Edit Post or Edit Page screen. Add your caption inside the text area within the meta box.

**How do I customize the formatting of the caption?**
By default, the entire caption HTML is wrapped in a `<div>` tag, but this can be toggled in plugin options. If this is enabled, the caption HTML will look like this:

    <div class="cc-featured-image-caption">
        <span class="cc-featured-image-caption-text">Caption text</span>
    </div>

If you have source attribution text but no URL, it will look like this:

    <div class="cc-featured-image-caption">
        <span class="cc-featured-image-caption-text">Caption text</span>
        <span class="cc-featured-image-caption-source">Attribution text</span>
    </div>

If you have a source attribution URL also, it will look like this:

    <div class="cc-featured-image-caption">
        <span class="cc-featured-image-caption-text">Caption text</span>
        <span class="cc-featured-image-caption-source"><a href="http://example.com/">Attribution text</a></span>
    </div>

If you have disabled the `<div>` container, everything inside the `<div>` will be the same but without the `<div>` around it.

**Can I customize where the caption appears on my site?**
By default, the plugin automatically adds the caption immediately after the featured image. You can change this in plugin options.

If you need to customize the placement of the caption, you can disable this option in plugin options, and place the following function in your theme where you would like the caption to appear:

    <?php cc_featured_image_caption(); ?>

**How do I return the value of the caption without displaying it?**
The `cc_featured_image_caption()` function accepts an argument to determine whether the result is displayed or returned.

To return the value, use the following syntax:

    <?php cc_featured_image_caption( false ); ?>

By default, the returned data will be the fully formatted HTML of the caption. If you want the raw array of the caption data, use the following syntax:

    <?php cc_featured_image_caption( false, false ); ?>
