<?php

namespace PluginPress\PluginPressAPI\PluginOptions;

use Exception;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

class PluginOptions
{
    private $plugin_options = [];

    public function __construct(
        private string $plugin_file_path,
        private string $config_file_path,
    )
    {
        if(empty($plugin_file_path) || !file_exists($plugin_file_path))
        {
            throw new Exception('Provided path for the current plugin file does not exist or the file path is invalid or empty.');
        }
        if(empty($config_file_path) || !file_exists($config_file_path))
        {
            throw new Exception('Provided config file does not exist or the file path is invalid or empty.');
        }
        $this->plugin_options = $this->_get_plugin_data(plugin_file_path : $plugin_file_path, config_file_path : $config_file_path);
    }

    private function _get_plugin_data(string $plugin_file_path, string $config_file_path) : array
    {
        $plugin_file_path = str_replace('\\', "/", $plugin_file_path);
        $config_file_path = str_replace('\\', "/", $config_file_path);
        $directory_tree = explode("/", $plugin_file_path);
        $plugin_file_name = end($directory_tree);
        array_pop($directory_tree);
        $plugin_dir_name = end($directory_tree);
        $required_plugin_data = [
            // Plugin basename. sanitize_key('basename')
            'plugin_base_name' => $plugin_dir_name . "/" . $plugin_file_name,
            // Plugin directory name
            'plugin_dir_name' => $plugin_dir_name,
            // Plugin file name
            'plugin_file_name' => str_replace('.php', '', $plugin_file_name),
            // Plugin directory url
            'plugin_dir_url' => plugin_dir_url($plugin_file_path),
            // Plugin directory path
            'plugin_dir_path' => plugin_dir_path($plugin_file_path),
            // Plugin file path
            'plugin_file_path' => $plugin_file_path,
            // For plugin version compatibility check
            'plugin_disabled' => false,
        ];
        $default_header_keys = [
            // Plugin name.
            'plugin_name' => 'Plugin Name',
            // Plugin description
            'plugin_description' => 'Description',
            // Plugin URL
            'plugin_url' => 'Plugin URI',
            // Current plugin version. update it as you release new versions.
            'plugin_version' => 'Version',
            // Minimum required version of WordPress.
            'plugin_requires_wordpress' => 'Requires at least',
            // Minimum required version of PHP.
            'plugin_requires_php' => 'Requires PHP',
            // Plugin author name
            'plugin_author_name' => 'Author',
            // Plugin author URL
            'plugin_author_url' => 'Author URI',
            // Plugin text domain. Max 20 Char
            'plugin_text_domain' => 'Text Domain',
            // Plugins relative directory path to .mo files.
            'plugin_text_domain_path' => 'Domain Path',
            // Allows third-party plugins to avoid accidentally being overwritten with an update of a plugin of a similar name from the WordPress.org Plugin Directory.
            'plugin_update_uri' => 'Update URI',
            // Whether the plugin can only be activated network-wide.
            'plugin_network' => 'Network',
            //  The short name (slug) of the plugin’s license (e.g. GPLv3). More information about licensing can be found in the WordPress.org guidelines.
            'plugin_license' => 'License',
            // A link to the full text of the license.
            'plugin_license_uri' => 'License URI',
        ];

        $plugin_meta_data = get_file_data(file : $plugin_file_path, default_headers : $default_header_keys, context : 'plugin');
        $custom_plugin_options = include_once $config_file_path;
        return array_merge($required_plugin_data, $plugin_meta_data, $custom_plugin_options);
    }

    public function get_plugin_options() : array
    {
        return $this->plugin_options;
    }

    public function get(string $option_name) : mixed
    {
        // HOOK: Filter - plugin_options_{PLUGIN_SLUG}
        $this->plugin_options = apply_filters(hook_name : 'plugin_options_' . $this->plugin_options['plugin_slug'], value : $this->plugin_options);
        if(array_key_exists($option_name, $this->plugin_options))
        {
            return $this->plugin_options[$option_name];
        }else
        {
            throw new Exception("Trying to access an undefined plugin option. add '$option_name' and 'VALUE' to your plugin config file.");
        }
    }

    // TODO: Implement the set option function
    // public function set(string $option_name, mixed $option_value = null)
    // {
        // add_filter(hook_name : 'plugin_options_' . $this->plugin_options['plugin_slug'], callback : ($current_plugin_options){
            
        //     $current_plugin_options[$option_name] = $option_value ;
        // }
        // );
        // $this->plugin_options[ $option_name ] = $option_value ;
        // return $this->get( $option_name );
    // }
}