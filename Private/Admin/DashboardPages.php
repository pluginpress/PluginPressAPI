<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die('Unauthorized access..!');
}

class DashboardPages extends DashboardSettings
{
    private     $admin_option_pages     = [];
    private     $admin_menu_pages       = [];
    private     $admin_submenu_pages    = [];
    private     $admin_enqueue_scripts  = [];
    protected   $registered_pages       = [];

    public function __construct(private PluginOptions $plugin_options)
    {
        parent::__construct(plugin_options : $plugin_options);
        $this->_add_default_admin_enqueue_scripts();
    }

    public function init() : void
    {
        if(!empty($this->admin_option_pages))
        {
            add_action(
                hook_name       : 'admin_menu',
                callback        : array($this,'register_option_pages'),
                priority        : 20,
                accepted_args   : 1,
            );
        }
        if(!empty($this->admin_menu_pages))
        {
            add_action(
                hook_name       : 'admin_menu',
                callback        : array($this, 'register_menu_pages'),
                priority        : 30,
                accepted_args   : 1,
            );
        }
        if(!empty($this->admin_submenu_pages))
        {
            add_action(
                hook_name       : 'admin_menu',
                callback        : array($this, 'register_submenu_pages'),
                priority        : 40,
                accepted_args   : 1,
            );
        }
        if(!empty($this->admin_enqueue_scripts))
        {
            add_action(
                hook_name       : 'admin_enqueue_scripts',
                callback        : array($this, 'register_admin_enqueue_scripts'),
                priority        : 10,
                accepted_args   : 1,
            );
        }
        parent::init();
    }

    public function add_option_page(
        string              $page_slug,                  // required - 
        string              $page_title,                 // required - 
        int                 $page_position      = 10,
        string              $page_menu_title    = '',
        string              $page_description   = '',
        string              $page_capabilities  = '',
        string | callable   $page_ui            = '',   // default empty - valid callback function || absolute path to the template file.
    ) : void
    {
        $page_ui_template = '';
        if($page_ui == '')
        {
            $page_ui = [$this, 'render_dashboard_page_ui'];
        }
        if(\is_string($page_ui) && is_file($page_ui))
        {
            $page_ui_template   = $page_ui;
            $page_ui            = [$this, 'render_dashboard_page_ui'];
        }
        $option_page = [
            'page_slug'             => $this->get_clean_slug(slug : $page_slug),
            'page_title'            => $page_title,
            'page_position'         => $page_position,
            'page_menu_title'       => $page_menu_title == '' ? $page_title : $page_menu_title,
            'page_description'      => $page_description,
            'page_capabilities'     => $page_capabilities == '' ? 'manage_options' : $page_capabilities,
            'page_ui'               => $page_ui,
            'page_ui_template'      => $page_ui_template,
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
            $this->_update_registered_page_suffix(page : $page);
        }
    }

    public function add_menu_page(
        string              $page_slug,                     // required - 
        string              $page_title,                    // required - 
        int                 $page_position          = 10,
        string              $page_icon_url          = '',
        string              $page_menu_title        = '',
        string              $page_description       = '',
        string              $page_capabilities      = '',
        string | callable   $page_ui                = '',   // default empty - valid callback function || absolute path to the template file.
    ) : void
    {
        $page_ui_template = '';
        if($page_ui == '')
        {
            $page_ui = [$this, 'render_dashboard_page_ui'];
        }
        if(\is_string($page_ui) && is_file($page_ui))
        {
            $page_ui_template   = $page_ui;
            $page_ui            = [$this, 'render_dashboard_page_ui'];
        }
        $menu_page = [
            'page_slug'             => $this->get_clean_slug(slug : $page_slug),
            'page_title'            => $page_title,
            'page_position'         => $page_position,
            'page_icon_url'         => $page_icon_url == '' ? 'dashicons-admin-generic' : $page_icon_url,
            'page_menu_title'       => $page_menu_title == '' ? $page_title : $page_menu_title,
            'page_description'      => $page_description,
            'page_capabilities'     => $page_capabilities == '' ? 'manage_options' : $page_capabilities,
            'page_ui'               => $page_ui,
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
            $this->_update_registered_page_suffix(page :  $page);
        }
    }

    public function add_submenu_page(
        string              $page_slug,                     // required - 
        string              $page_title,                    // required - 
        string              $page_parent_slug,              // required - 
        int                 $page_position          = 10,
        string              $page_menu_title        = '',
        string              $page_description       = '',
        string              $page_capabilities      = '',
        string | callable   $page_ui                = '',   // default empty - valid callback function || absolute path to the template file.
    ) : void
    {
        $page_ui_template = '';
        if($page_ui == '')
        {
            $page_ui = [$this, 'render_dashboard_page_ui'];
        }
        if(\is_string($page_ui) && is_file($page_ui))
        {
            $page_ui_template   = $page_ui;
            $page_ui            = [$this, 'render_dashboard_page_ui'];
        }
        $submenu_page = [
            'page_slug'             => $this->get_clean_slug(slug : $page_slug),
            'page_title'            => $page_title,
            'page_parent_slug'      => $this->get_clean_slug(slug : $page_parent_slug),
            'page_position'         => $page_position,
            'page_menu_title'       => $page_menu_title == '' ? $page_title : $page_menu_title,
            'page_description'      => $page_description,
            'page_capabilities'     => $page_capabilities == '' ? 'manage_options' : $page_capabilities,
            'page_ui'               => $page_ui,
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
            $this->_update_registered_page_suffix(page : $page);
        }
    }

    public function add_admin_enqueue_scripts(
        string      $enqueue_script_slug,
        string      $enqueue_script_path,
        string      $enqueue_script_type,
        string      $page_slug              = '',
        string      $enqueue_location       = 'top',
    ) : void
    {
        $enqueue_script = [
            'enqueue_script_slug'       => $this->get_clean_slug(slug : $enqueue_script_slug),
            'enqueue_script_path'       => $enqueue_script_path,
            'enqueue_script_type'       => $enqueue_script_type,
            'page_slug'                 => $page_slug == '' ? 'default' : $page_slug,
            'enqueue_location'          => $enqueue_location,
        ];
        array_push($this->admin_enqueue_scripts, $enqueue_script);
        $this->init();
        ;
    }

    public function register_admin_enqueue_scripts() : void
    {
        foreach($this->admin_enqueue_scripts as $enqueue_script)
        {
            foreach($this->registered_pages as $key => $page)
            {
                if($enqueue_script['page_slug'] == $page['page_slug'] || $enqueue_script['page_slug'] == 'default')
                {
                    $this->_update_registered_page_enqueue_scripts(current_page : $key, enqueue_script : $enqueue_script);
                }
                add_action(
                    hook_name   : 'admin_head-' . $page['page_hook_suffix'],
                    callback    : array($this, 'render_header_enqueued_scripts'),
                );
                add_action(
                    hook_name   : 'admin_footer-' . $page['page_hook_suffix'],
                    callback    : array($this, 'render_footer_enqueued_scripts'),
                );
            }
        }
    }

    // user will redirected to the welcome page when plugin is activated.
    // If a plugin is silently activated (such as during an update, multisite, or multiple plugin activation), this does not redirect to the welcome page.
    public function add_plugin_welcome_page(
        string | callable   $page_ui,                       // required - valid callback function || absolute path to the template file.
        string              $page_title         = '',
        string              $page_menu_title    = '',
        bool                $page_show_always   = false,
    ) : void
    {
        $this->add_submenu_page(
            page_slug           :'welcome_page',
            page_title          : $page_title == '' ? 'Welcome to ' . $this->plugin_options->get('plugin_title') : $page_title,
            page_parent_slug    : 'index.php',
            page_menu_title     : $page_menu_title == '' ? 'Welcome to ' . $this->plugin_options->get('plugin_title') : $page_menu_title,
            page_ui             : $page_ui,
        );
        if($page_show_always == false)
        {
            add_action(
                hook_name       : 'admin_head',
                callback        : function()
                    {
                        remove_submenu_page(menu_slug : 'index.php', submenu_slug : $this->get_clean_slug('welcome_page'));
                    },
                priority        : 10,
                accepted_args   : 1,
            );
        }
        add_action(
            hook_name       : 'admin_init',
            callback        : function()
                {
                    if(isset($_GET['activate-multi']))
                    {
                        return;
                    }
                    if(get_transient($this->plugin_options->get('plugin_slug') . '_welcome_page_auto_redirect') == true)
                    {
                        delete_transient($this->plugin_options->get('plugin_slug') . '_welcome_page_auto_redirect');
                        wp_safe_redirect(admin_url('index.php?page=' . $this->get_clean_slug(slug : 'welcome_page')));
                        exit;
                    }
                    return;
                },
            priority        : 10,
            accepted_args   : 1,
        );
        $this->init();
    }

    public function get_registered_pages() : array
    {
        return $this->registered_pages;
    }

    public function get_current_page() : array
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

    private function _update_registered_page_suffix(array $page) : void
    {
        if($page['page_hook_suffix'] == false)
        {
            // The user does not have the capability required to create a page.
            return;
        }
        array_push($this->registered_pages, $page);
    }

    private function _update_registered_page_enqueue_scripts(int $current_page, array $enqueue_script) : void
    {
        if(!\array_key_exists('enqueue_on_page_head', $this->registered_pages[$current_page]))
        {
            $this->registered_pages[$current_page]['enqueue_on_page_head'] = [];
        }
        if(!\array_key_exists('enqueue_on_page_footer', $this->registered_pages[$current_page]))
        {
            $this->registered_pages[$current_page]['enqueue_on_page_footer'] = [];
        }
        if($enqueue_script['enqueue_location'] == 'top')
        {
            array_push($this->registered_pages[$current_page]['enqueue_on_page_head'], $enqueue_script);
        }
        if($enqueue_script['enqueue_location'] == 'bottom')
        {
            array_push($this->registered_pages[$current_page]['enqueue_on_page_footer'], $enqueue_script);
        }
    }

    private function _add_default_admin_enqueue_scripts() : void
    {
        $this->add_admin_enqueue_scripts(
            enqueue_script_slug : 'dashboard_ui',
            enqueue_script_path : $this->plugin_options->get('plugin_dir_url') . 'vendor/pluginpress/pluginpressapi/Public/Assets/StyleSheets/DashboardUI.css',
            enqueue_script_type : 'style',
            page_slug           : 'default' ,
            enqueue_location    : 'top',
        );
        $this->add_admin_enqueue_scripts(
            enqueue_script_slug : 'dashboard_ui',
            enqueue_script_path : $this->plugin_options->get('plugin_dir_url') . 'vendor/pluginpress/pluginpressapi/Public/Assets/JavaScripts/DashboardUI.js',
            enqueue_script_type : 'script',
            page_slug           : 'default' ,
            enqueue_location    : 'bottom',
        );
    }
}
