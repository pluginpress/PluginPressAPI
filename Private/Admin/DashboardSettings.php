<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;
use PluginPress\PluginPressAPI\Traits\Utilities;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die('Unauthorized access..!');
}

class DashboardSettings
{
    use DashboardUI;
    use Utilities;

    protected       $tabs       = [];
    protected       $sections   = [];
    protected       $options    = [];
    
    public function __construct(private PluginOptions $plugin_options)
    {
    }

    public function init() : void
    {
        if(!empty($this->tabs))
        {
            add_action(
                hook_name       : 'admin_init',
                callback        : array($this, 'register_tabs'),
                priority        : 11,
                accepted_args   : 1,
            );
        }
        if(!empty($this->sections))
        {
            add_action(
                hook_name       : 'admin_init',
                callback        : array($this, 'register_sections'),
                priority        : 12,
                accepted_args   : 1,
            );
        }
        if(!empty($this->options))
        {
            add_action(
                hook_name       : 'admin_init',
                callback        : array($this, 'register_options'),
                priority        : 13,
                accepted_args   : 1,
            );
        }
    }

    public function add_tab(
        string      $tab_slug,                              // required - 
        string      $tab_title,                             // required - 
        string      $tab_parent_page_slug,                  // required - 
        string      $tab_description            = '',
        bool        $tab_default                = false,
    ) : void
    {
        $tab = [
            'tab_slug'                  => $this->get_clean_slug(slug : $tab_parent_page_slug) . '_' . $tab_slug,
            'tab_title'                 => $tab_title,
            'tab_parent_page_slug'      => $this->get_clean_slug(slug : $tab_parent_page_slug),
            'tab_description'           => $tab_description,
            'tab_default'               => $tab_default,
        ];
        array_push($this->tabs, $tab);
        $this->init();
    }

    public function register_tabs() : void
    {
        // Creating global WP array variable to hold all the tabs. this variable dos'n include in WordPress by default. so it won't use by WordPress.
        foreach($this->tabs as $tab)
        {
            $GLOBALS['wp_settings_tabs'][$tab['tab_parent_page_slug']][$tab['tab_slug']] = array(
                'tab_slug'                  => $tab['tab_slug'],
                'tab_title'                 => $tab['tab_title'],
                'tab_parent_page_slug'      => $tab['tab_parent_page_slug'],
                'tab_description'           => $tab['tab_description'],
                'tab_default'               => $tab['tab_default'],
            );
        }
    }

    public function add_section(
        string              $section_slug,                          // required - 
        string              $section_title,                         // required - 
        string              $section_parent_page_slug,              // required - 
        string              $section_parent_tab_slug        = '',
        string              $section_description            = '',
        string | callable   $section_ui                     = '',
    ) : void
    {
        $section_parent_page_slug   = $this->get_clean_slug(slug : $section_parent_page_slug);
        $section_parent_tab_slug    = $section_parent_tab_slug == '' ? $section_parent_page_slug : $section_parent_page_slug . '_' . $section_parent_tab_slug;
        $section_ui_template        = '';
        if($section_ui == '')
        {
            $section_ui = [$this, 'render_after_section_header'];
        }
        if(\is_string($section_ui) && is_file($section_ui))
        {
            $section_ui_template   = $section_ui;
            $section_ui            = [$this, 'render_after_section_header'];
        }
        $section = [
            'section_slug'                  => $section_parent_page_slug . '_' . $section_slug,
            'section_title'                 => $section_title,
            'section_parent_page_slug'      => $section_parent_page_slug,
            'section_parent_tab_slug'       => $section_parent_tab_slug,
            'section_description'           => $section_description,
            'section_ui'                    => $section_ui,
            'section_ui_template'           => $section_ui_template,
        ];
        array_push($this->sections, $section);
        $this->init();
    }

    public function register_sections() : void
    {
        global $wp_settings_sections;
        foreach($this->sections as $section)
        {
            $parent_slug   = $section['section_parent_page_slug'];
            $section_slug  = $section['section_slug'];
            if(!isset($wp_settings_sections[$parent_slug][$section_slug]))
            {
                $wp_settings_sections[$parent_slug][$section_slug] = [];
            }
            // default WordPress key and values. this normally add by add_settings_section();
            $wp_settings_sections[$parent_slug][$section_slug]['id']                         = $section['section_slug'];
            $wp_settings_sections[$parent_slug][$section_slug]['title']                      = $section['section_title'];
            $wp_settings_sections[$parent_slug][$section_slug]['callback']                   = $section['section_ui'];
            $wp_settings_sections[$parent_slug][$section_slug]['page']                       = $section['section_parent_page_slug'];
            // default PluginPress key and values
            $wp_settings_sections[$parent_slug][$section_slug]['section_slug']               = $section['section_slug'];
            $wp_settings_sections[$parent_slug][$section_slug]['section_title']              = $section['section_title'];
            $wp_settings_sections[$parent_slug][$section_slug]['section_parent_page_slug']   = $section['section_parent_page_slug'];
            $wp_settings_sections[$parent_slug][$section_slug]['section_parent_tab_slug']    = $section['section_parent_tab_slug'];
            $wp_settings_sections[$parent_slug][$section_slug]['section_description']        = $section['section_description'];
            $wp_settings_sections[$parent_slug][$section_slug]['section_ui']                 = $section['section_ui'];
            $wp_settings_sections[$parent_slug][$section_slug]['section_ui_template']        = $section['section_ui_template'];
        }
    }

    public function add_option(
        string              $option_slug,                               // required - 
        string              $option_title,                              // required - 
        string              $option_parent_page_slug,                   // required - 
        bool                $option_hidden                  = false,
        bool                $option_checked                 = false,
        bool                $option_disabled                = false,
        bool                $option_required                = false,
        bool                $option_readonly                = false,
        bool                $option_show_in_rest            = false,
        bool                $option_default_value           = false,
        bool                $option_content_editable        = false,
        string | callable   $option_ui                      = '',
        string              $option_min                     = '',
        string              $option_max                     = '',
        string              $option_list                    = '',
        string              $option_type                    = 'text',
        string              $option_style                   = '',
        string              $option_class                   = '',
        string              $option_label_for               = '',
        string              $option_data_type               = 'string',
        string              $option_max_length              = '',
        string              $option_min_length              = '',
        string              $option_placeholder             = 'Enter option here',
        string              $option_description             = '',
        string              $option_parent_tab_slug         = '',
        string              $option_parent_section_slug     = '',
        string | callable   $option_sanitize_callback       = '',
    ) : void
    {
        $option_parent_page_slug    = $this->get_clean_slug(slug : $option_parent_page_slug);
        $option_ui_template         = '';
        if($option_ui == '')
        {
            $option_ui = [$this, 'render_option_ui'];
        }
        if(\is_string($option_ui) && is_file($option_ui))
        {
            $option_ui_template   = $option_ui;
            $option_ui            = [$this, 'render_option_ui'];
        }
        $option = [
            'option_slug'                   => $option_parent_page_slug . '_' . $option_slug,
            'option_title'                  => $option_title,
            'option_parent_page_slug'       => $option_parent_page_slug,
            'option_hidden'                 => $option_hidden,
            'option_checked'                => $option_checked,
            'option_disabled'               => $option_disabled,
            'option_required'               => $option_required,
            'option_readonly'               => $option_readonly,
            'option_show_in_rest'           => $option_show_in_rest,
            'option_default_value'          => $option_default_value,
            'option_content_editable'       => $option_content_editable,
            'option_ui'                     => $option_ui,
            'option_min'                    => $option_min,
            'option_max'                    => $option_max,
            'option_list'                   => $option_list,
            'option_type'                   => $option_type,
            'option_style'                  => $option_style,
            'option_class'                  => $option_class,
            'option_label_for'              => $option_label_for == ''
                                                ? $option_parent_page_slug . '_' . $option_slug
                                                : $option_parent_page_slug . '_' . $option_label_for,
            'option_data_type'              => $option_data_type,
            'option_max_length'             => $option_max_length,
            'option_min_length'             => $option_min_length,
            'option_placeholder'            => $option_placeholder,
            'option_description'            => $option_description,
            'option_parent_tab_slug'        => $option_parent_tab_slug == ''
                                                ? $option_parent_page_slug
                                                : $option_parent_page_slug . '_' . $option_parent_tab_slug,
            'option_parent_section_slug'    => $option_parent_section_slug == '' ? 'default' : $option_parent_page_slug . '_' . $option_parent_section_slug,
            'option_sanitize_callback'      => $option_sanitize_callback == '' ? [$this, 'option_sanitize_callback'] : $option_sanitize_callback,
            'option_ui_template'            => $option_ui_template,
        ];
        array_push($this->options, $option);
        $this->init();
    }

    public function register_options() : void
    {
        foreach($this->options as $option)
        {
            register_setting(
                option_group    : $option['option_parent_tab_slug'],
                option_name     : $option['option_slug'],
                args            : [
                    'type'              => $option['option_data_type'],
                    'description'       => $option['option_description'],
                    'sanitize_callback' => $option['option_sanitize_callback'],
                    'show_in_rest'      => $option['option_show_in_rest'],
                    'default'           => $option['option_default_value'],
                ]
            );
            add_settings_field(
                id          : $option['option_slug'],
                title       : $option['option_title'],
                callback    : $option['option_ui'],
                page        : $option['option_parent_page_slug'],
                section     : $option['option_parent_section_slug'],
                args        : $option
            );
        }
    }

    protected function get_current_page_tabs(array $current_page) : array | bool
    {
        if(empty($this->tabs))
        {
            return false; // no tabs for any page.
        }
        $page_tabs = [];
        foreach($this->tabs as $tab)
        {
            if($tab['tab_parent_page_slug'] == $current_page['page_slug'])
            {
                array_push($page_tabs, $tab);
            }
        }
        if(empty($page_tabs))
        {
            return false; // no tabs for current page.
        }
        return $page_tabs;
    }

    protected function get_registered_page_sections(array $page) : array | bool
    {
        global $wp_settings_sections;
        if(!isset($wp_settings_sections[$page['page_slug']]))
        {
            // This page don't have registered settings.
            return false; 
        }
        $page_sections = [];
        foreach($this->sections as $section)
        {
            if($section['section_parent_page_slug'] == $page['page_slug'])
            {
                array_push($page_sections, $section);
            }
        }
        if(empty($page_sections))
        {
            // current page don't have settings.
            return false; 
        }
        return $page_sections;
    }

    protected function get_current_page_options(array $current_page) : array
    {
        $page_options = [];
        foreach($this->options as $option)
        {
            if($option['option_parent_page_slug'] == $current_page['page_slug'])
            {
                array_push($page_options, $option);
            }
        }
        return $page_options;
    }

    protected function get_active_tab(array $page_tabs) : array | bool
    {
        $active_tab_slug = (isset($_GET['tab']) ? $_GET['tab'] : null);
        $active_tab = [];
        if(empty($page_tabs))
        {
            return false;
        }
        if($active_tab_slug == null)
        {
            foreach($page_tabs as $tab)
            {
                if(isset($tab['tab_default']) && $tab['tab_default'] == true)
                {
                    $active_tab = $tab;
                }
            }
            if(empty($active_tab))
            {
                // If no default tab is defined, first tab will set as a default tab
                $active_tab = $page_tabs[0];
            }
        }
        else
        {
            foreach($page_tabs as $tab)
            {
                if($tab['tab_slug'] == $active_tab_slug)
                {
                    $active_tab = $tab;
                }
            }
        }
        return $active_tab;
    }

    // TODO: implement the user input data sanitization function for input fields
    public function option_sanitize_callback($data)
    {
        return esc_html__($data, $this->plugin_options->get('plugin_text_domain'));
    }
}