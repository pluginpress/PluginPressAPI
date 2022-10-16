<?php

namespace PluginPress\PluginPressAPI\PluginActivator;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;

/**
 * If this file is called directly, abort! for security purposes.
 */

if(!defined('WPINC'))
{
    die('Unauthorized access..!');
}

class PluginActivator
{
    private $activation_hook_class;
    private $deactivation_hook_class;

    public function __construct(protected PluginOptions $plugin_options)
    {
    }

    public function init() : void
    {
        // If a plugin is silently activated (such as during an update), this hook does not fire.
        register_activation_hook(file : $this->plugin_options->get('plugin_base_name'), callback : [$this, 'do_activation_hook']);
        // If a plugin is silently deactivated (such as during an update), this hook does not fire.
        register_deactivation_hook(file : $this->plugin_options->get('plugin_base_name'), callback : [$this, 'do_deactivation_hook']);
    }
    
    public function do_activation_hook() : void
    {
        if(is_object($this->activation_hook_class))
        {
            $this->activation_hook_class->init();
        }
    }

    public function do_deactivation_hook() : void
    {
        if(is_object($this->deactivation_hook_class))
        {
            $this->deactivation_hook_class->init();
        }
    }
    public function set_activation_hook($activation_hook_class) : void
    {
        $this->activation_hook_class = $activation_hook_class;
    }

    public function set_deactivation_hook($deactivation_hook_class) : void
    {
        $this->deactivation_hook_class = $deactivation_hook_class;
    }
}
