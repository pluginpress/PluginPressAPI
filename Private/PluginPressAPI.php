<?php

namespace PluginPress\PluginPressAPI;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;
use PluginPress\PluginPressAPI\PluginActivator\PluginActivator;
use PluginPress\PluginPressAPI\WordPress\PluginsPageCustomizer;
use PluginPress\PluginPressAPI\Admin\DashboardPages;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

class PluginPressAPI
{
    protected $plugin_options;
    protected $plugin_activator;
    protected $plugins_page_customizer;
    protected $dashboard_pages;

    public function __construct(
        protected string $plugin_file_path,
        protected string $config_file_path
    )
    {
        $this->plugin_options = new PluginOptions(plugin_file_path : $plugin_file_path, config_file_path : $config_file_path);
        $this->plugin_activator = new PluginActivator(plugin_options : $this->plugin_options);
        $this->plugins_page_customizer = new PluginsPageCustomizer(plugin_options : $this->plugin_options);
        $this->dashboard_pages = new DashboardPages(plugin_options : $this->plugin_options);






        // print('<pre>');
        // var_dump($this->dashboard_pages);
        // print('</pre>');
        // die;
        


    }
}