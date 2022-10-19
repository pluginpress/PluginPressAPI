<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

class DashboardEnqueueScripts
{
    public function __construct(private PluginOptions $plugin_options)
    {
    }

    public function init()
    {
        add_action('admin_enqueue_scripts', array($this, 'register_pluginpress_api_default_scripts'));
    }

    public function register_pluginpress_api_default_scripts($hooks) : void
    {
        wp_enqueue_style(
            handle : $this->plugin_options->get('plugin_slug') . '_default_dashboard_ui',
            src : $this->plugin_options->get('plugin_dir_url') . 'vendor/pluginpress/pluginpressapi/Public/AdminAssets/StyleSheets/DashboardUI.css'
        );
        wp_enqueue_script(
            handle : $this->plugin_options->get('plugin_slug') . '_default_dashboard_ui',
            src : $this->plugin_options->get('plugin_dir_url') . 'vendor/pluginpress/pluginpressapi/Public/AdminAssets/JavaScripts/DashboardUI.js'
        );
    }
}