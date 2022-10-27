<?php

namespace PluginPress\PluginPressAPI\Traits;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die('Unauthorized access..!');
}

trait Utilities
{
    public function get_clean_slug(string $slug) : string
    {
        $slug = \strtolower($slug);
        if(\str_ends_with($slug, '.php'))
        {
            return $slug;
        }
        if(\str_starts_with($slug, $this->plugin_options->get('plugin_slug')))
        {
            return $slug;
        }
        return $this->plugin_options->get('plugin_slug') . '_' . $slug;
    }
}