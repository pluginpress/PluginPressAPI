<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;

// If this file is called directly, abort. for the security purpose.
if( ! defined( 'WPINC' ) )
{
    die;
}

class AdminPages extends AdminSettings
{

    use AdminPagesUI;

    private $plugin_options;
    private $admin_enqueue_scripts = [];
    private $admin_option_pages = [];
    private $admin_pages = [];
    private $admin_sub_pages = [];
    private $registered_pages = [];

    public function __construct( PluginOptions $plugin_options )
    {
        $this->plugin_options = $plugin_options;
        parent::__construct( $plugin_options );
    }

    public function init()
    {


    }

    // TODO: Implements the function to add page/tabs/sections/fields at once.














}