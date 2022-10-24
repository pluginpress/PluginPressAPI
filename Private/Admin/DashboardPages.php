<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;
use PluginPress\PluginPressAPI\Traits\Utilities;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

class DashboardPages extends DashboardSettings
{
    use DashboardUI;
    use Utilities;

    private $admin_option_pages = [];
    private $admin_menu_pages = [];
    private $admin_submenu_pages = [];
    private $registered_pages = [];

    public function __construct(private PluginOptions $plugin_options)
    {
        parent::__construct($plugin_options);
    }

    public function init()
    {
        if(!empty($this->admin_option_pages))
        {
            add_action('admin_menu', array($this,'register_option_pages'), 20);
        }
        if(!empty($this->admin_menu_pages))
        {
            add_action('admin_menu', array($this, 'register_menu_pages'), 30);
        }
        if(!empty($this->admin_submenu_pages))
        {
            add_action('admin_menu', array($this, 'register_submenu_pages'), 40);
        }
        parent::init();
    }

    public function add_option_page(
        string $page_slug,                  // required - 
        string $page_title,                 // required - 
        string $page_ui             = '',   // default empty - valid callback function || absolute path to the template file.
        string $page_menu_title     = '',
        string $page_description    = '',
        string $page_capabilities   = '',
        int $page_position          = 10,
    ) : void
    {
        $page_ui                = $page_ui == '' ? [$this, 'render_dashboard_page'] : $page_ui;
        $page_ui_template       = '';
        if(!is_array($page_ui) && is_file($page_ui))
        {
            $page_ui_template   = $page_ui;
            $page_ui            = [$this, 'render_dashboard_page'];
        }
        $option_page = [
            'page_slug'             => $this->get_clean_slug($page_slug),
            'page_title'            => $page_title,
            'page_ui'               => $page_ui,
            'page_menu_title'       => $page_menu_title == '' ? $page_title : $page_menu_title,
            'page_description'      => $page_description,
            'page_capabilities'     => $page_capabilities == '' ? 'manage_options' : $page_capabilities,
            'page_ui_template'      => $page_ui_template,
            'page_position'         => $page_position,
            'page_type'             => 'option_page',
        ];
        array_push($this->admin_option_pages, $option_page);
        $this->init();
    }

    public function register_option_pages() : void
    {
        foreach($this->admin_option_pages as $page)
        {
            $page['page_hook_suffix'] = add_options_page(
                page_title      : $page['page_title'],
                menu_title      : $page['page_menu_title'],
                capability      : $page['page_capabilities'],
                menu_slug       : $page['page_slug'],
                callback        : $page['page_ui'],
                position        : $page['page_position'],
            );
            $this->_update_registered_pages($page);
        }
    }

    public function add_menu_page(
        string $page_slug,                  // required - 
        string $page_title,                 // required - 
        string $page_ui             = '',   // default empty - valid callback function || absolute path to the template file.
        string $page_icon_url       = '',
        string $page_menu_title     = '',
        string $page_description    = '',
        string $page_capabilities   = '',
        int $page_position          = 10,
    ) : void
    {
        $page_ui = $page_ui == '' ? [$this, 'render_dashboard_page'] : $page_ui;
        $page_ui_template = '';
        if(!is_array($page_ui) && is_file($page_ui))
        {
            $page_ui_template = $page_ui;
            $page_ui = [$this, 'render_dashboard_page'];
        }
        $menu_page = [
            'page_slug'             => $this->get_clean_slug($page_slug),
            'page_title'            => $page_title,
            'page_ui'               => $page_ui,
            'page_icon_url'         => $page_icon_url == '' ? 'dashicons-admin-generic' : $page_icon_url,
            'page_menu_title'       => $page_menu_title == '' ? $page_title : $page_menu_title,
            'page_description'      => $page_description,
            'page_capabilities'     => $page_capabilities == '' ? 'manage_options' : $page_capabilities,
            'page_position'         => $page_position,
            'page_ui_template'      => $page_ui_template,
            'page_type'             => 'menu_page',
        ];
        array_push($this->admin_menu_pages, $menu_page);
        $this->init();
    }

    public function register_menu_pages() : void
    {
        foreach($this->admin_menu_pages as $page)
        {
            $page['page_hook_suffix'] = add_menu_page(
                page_title      : $page['page_title'],
                menu_title      : $page['page_menu_title'],
                capability      : $page['page_capabilities'],
                menu_slug       : $page['page_slug'],
                callback        : $page['page_ui'],
                icon_url        : $page['page_icon_url'],
                position        : $page['page_position'],
            );
            $this->_update_registered_pages($page);
        }
    }

    public function add_submenu_page(
        string $page_parent_slug,           // required - 
        string $page_slug,                  // required - 
        string $page_title,                 // required - 
        string $page_ui             = '',   // default empty - valid callback function || absolute path to the template file.
        string $page_menu_title     = '',
        string $page_description    = '',
        string $page_capabilities   = '',
        int $page_position          = 10,
    ) : void
    {
        $page_ui            = $page_ui == '' ? [$this, 'render_dashboard_page'] : $page_ui;
        $page_ui_template   = '';
        if(!is_array($page_ui) && is_file($page_ui))
        {
            $page_ui_template   = $page_ui;
            $page_ui            = [$this, 'render_dashboard_page'];
        }
        $submenu_page = [
            'page_parent_slug'      => $this->get_clean_slug($page_parent_slug),
            'page_slug'             => $this->get_clean_slug($page_slug),
            'page_title'            => $page_title,
            'page_ui'               => $page_ui,
            'page_menu_title'       => $page_menu_title == '' ? $page_title : $page_menu_title,
            'page_description'      => $page_description,
            'page_capabilities'     => $page_capabilities == '' ? 'manage_options' : $page_capabilities,
            'page_position'         => $page_position,
            'page_ui_template'      => $page_ui_template,
            'page_type'             => 'submenu_page',
        ];
        array_push($this->admin_submenu_pages, $submenu_page);
        $this->init();
    }

    public function register_submenu_pages() : void
    {
        foreach($this->admin_submenu_pages as $page)
        {
            $page['page_hook_suffix'] = add_submenu_page(
                parent_slug     : $page['page_parent_slug'],
                page_title      : $page['page_title'],
                menu_title      : $page['page_menu_title'],
                capability      : $page['page_capabilities'],
                menu_slug       : $page['page_slug'],
                callback        : $page['page_ui'],
                position        : $page['page_position']
            );
            $this->_update_registered_pages($page);
        }
    }



























    // user will redirected to the welcome page when plugin is activated.
    // If a plugin is silently activated (such as during an update, multisite, or multiple plugin activation), this does not redirect to the welcome page.
    public function add_plugin_welcome_page(
        string $page_ui, // required - valid callback function || absolute path to the template file.
        string $page_title = '',
        string $page_menu_title = '',
        bool $page_show_always = false,
    ) : void
    {
        $this->add_submenu_page(
                page_slug :'welcome_page',
                page_title : $page_title == '' ? 'Welcome to ' . $this->plugin_options->get('plugin_title') : $page_title,
                page_parent_slug : 'index.php',
                page_menu_title : $page_menu_title == '' ? 'Welcome to ' . $this->plugin_options->get('plugin_title') : $page_menu_title,
                page_ui : $page_ui,
        );
        if($page_show_always == false)
        {
            add_action(
                'admin_head',
                function()
                {
                    remove_submenu_page('index.php', $this->get_clean_slug('welcome_page'));
                }
            );
        }
        add_action(
            'admin_init',
            function()
            {
                if(isset($_GET['activate-multi']))
                {
                    return;
                }
                if(get_transient($this->plugin_options->get('plugin_slug') . '_welcome_page_auto_redirect') == true)
                {
                    delete_transient($this->plugin_options->get('plugin_slug') . '_welcome_page_auto_redirect');
                    wp_safe_redirect(admin_url('index.php?page=' . $this->get_clean_slug('welcome_page')));
                    exit;
                }
                return;
            }
        );
        $this->init();
    }


    public function get_registered_pages() : array
    {
        return $this->registered_pages;
    }

    protected function get_current_page() : array
    {
        $current_screen = get_current_screen();
        foreach($this->get_registered_pages() as $page)
        {
            if(isset($page['page_hook_suffix']) && $page['page_hook_suffix'] != false && $page['page_hook_suffix'] == $current_screen->id)
            {
                return $page;
            }
        }
        return [];
    }

    private function _update_registered_pages( $page ) : void
    {
        if($page['page_hook_suffix'] == false)
        {
            // The user does not have the capability required to create a page.
            return;
        }
        array_push($this->registered_pages, $page);
        // $this->enqueue_on_page( $page );
    }
    // private function enqueue_on_page( $page ) : void
    // {
    //     // Prints in head section for a specific admin page.
    //     if( isset( $page[ 'enqueue_on_page_head' ] ) && ! empty( $page[ 'enqueue_on_page_head' ] ) && is_array( $page[ 'enqueue_on_page_head' ] ) )
    //     {
    //         add_action(
    //             'admin_head-' . $page[ 'page_hook_suffix' ],
    //             function() use( $page )
    //             {
    //                 foreach( $page[ 'enqueue_on_page_head' ] as $script )
    //                 {
    //                     echo $script . '<br/>';
    //                 }
    //             } 
    //         );
    //     }
    //     // Prints scripts or data after the default footer scripts.
    //     if( isset( $page[ 'enqueue_on_page_footer' ] ) && ! empty( $page[ 'enqueue_on_page_footer' ] ) && is_array( $page[ 'enqueue_on_page_footer' ] ) )
    //     {
    //         add_action(
    //             'admin_footer-' . $page[ 'page_hook_suffix' ],
    //             function() use( $page )
    //             {
    //                 foreach( $page[ 'enqueue_on_page_footer' ] as $script )
    //                 {
    //                     echo $script . '<br/>';
    //                 }
    //             } 
    //         );
    //     }
    // }
}
