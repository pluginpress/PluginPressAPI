<?php

namespace PluginPress\PluginPressAPI;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

class PluginPressAPI
{
    protected $plugin_options;

    public function __construct(
        protected string $plugin_file_path,
        protected string $config_file_path
    )
    {
        // $this->plugin_options = new PluginOptions($plugin_file_path, $config_file_path);
    }
}