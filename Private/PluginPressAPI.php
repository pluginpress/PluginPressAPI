<?php

namespace PluginPress\PluginPressAPI;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;
use PluginPress\PluginPressAPI\PluginActivator\PluginActivator;
// use PluginPress\PluginPressAPI\WordPress\PluginsPageCustomizer;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

class PluginPressAPI
{
    protected $plugin_options;
    protected $plugin_activator;

    public function __construct(
        protected string $plugin_file_path,
        protected string $config_file_path
    )
    {
        $this->plugin_options = new PluginOptions(plugin_file_path : $plugin_file_path, config_file_path : $config_file_path);
        $this->plugin_activator = new PluginActivator($this->plugin_options);
        $this->plugin_activator->init();



        // print('<pre>');
        // var_dump($this->plugin_options);
        // print('</pre>');
        // die;
        


        // (new PluginsPageCustomizer($this->plugin_options))->init();
    }
}