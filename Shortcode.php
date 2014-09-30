<?php
class SV_Image_Hover_Shortcode
{
    public static $isEnqueueStyle;

    public static function getInstance()
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new static();
        }

        return $instance;
    }

    protected function __construct()
    {
        self::$isEnqueueStyle = false;
    }

function init()
{
    add_filter('img_caption_shortcode', array($this ,'img_caption_shortcode'), 10, 3);
}
function img_caption_shortcode($output, $atts, $content = null)
{
    if(!self::$isEnqueueStyle)
    {
        self::$isEnqueueStyle = true;
        wp_enqueue_style( "image-hover-style", plugins_url("style.css",__FILE__), array(), null, "screen");
    }
    if ($atts['width'] < 1 || empty($atts['caption']))
        return $content;

    if (!empty($atts['id']))
        $atts['id'] = 'id="' . esc_attr($atts['id']) . '" ';
    $id = explode('_', $atts['id'])[1];
    $meta = get_post($id);
    $description = ($meta->post_content ? '<span class="description">' . $meta->post_content . '</span>' : '');
    $class = trim($atts['align'] . ' ' . $atts['class'] . ' ' . 'sv_container hover-style-1');
    if (current_theme_supports('html5', 'caption')) {
        return '<figure ' . $atts['id'] . 'style="width: ' . (int)$atts['width'] . 'px;" class="' . esc_attr($class) . '">'
        . do_shortcode($content) . '<figcaption class="figcaption"><span class="caption">' . $atts['caption'] . '</span>' . $description . '</figcaption></figure>';
    }

    $caption_width = $atts['width'];

    /**
     * Filter the width of an image's caption.
     *
     * By default, the caption is 10 pixels greater than the width of the image,
     * to prevent post content from running up against a floated image.
     *
     * @since 3.7.0
     *
     * @see img_caption_shortcode()
     *
     * @param int $caption_width Width of the caption in pixels. To remove this inline style,
     *                              return zero.
     * @param array $atts Attributes of the caption shortcode.
     * @param string $content The image element, possibly wrapped in a hyperlink.
     */
    $caption_width = apply_filters('img_caption_shortcode_width', $caption_width, $atts, $content);

    $style = '';
    if ($caption_width)
        $style = 'style="width: ' . (int)$caption_width . 'px" ';

    return '<div ' . $atts['id'] . $style . 'class="' . esc_attr($class) . '">'
    . do_shortcode($content) . '<div class="figcaption"><span class="caption">' . $atts['caption'] . '</span>' . $description . '</div></div>';
}
}
