<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\UI\UI;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die;
}

trait DashboardUI
{
    public function render_dashboard_page() : void
    {

        $current_page = $this->get_current_page();
        if(!$current_page['page_ui_template'] == '' && \is_file($current_page['page_ui_template']))
        {
            include_once $current_page['page_ui_template'];
            return;
        }
        echo '<div class="wrap">';
        $this->render_page_header_section(current_page : $current_page);
        echo settings_errors();
        $current_page_tabs = $this->get_current_page_tabs($current_page);
        if($current_page_tabs)
        {
            $active_tab = $this->get_active_tab($current_page_tabs);
            if($active_tab)
            {
                $this->render_tabs();
            }
        }
        $this->render_sections(current_page : $current_page);

        $this->render_page_footer_section(current_page : $current_page);
        echo '</div>';
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




    public function render_page_header_section(array $current_page) : void
    {
        // HOOK: Action - before_dashboard_page_header_section_{PLUGIN_SLUG} - All dashboard page
        do_action('before_dashboard_page_header_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_header_section_{PAGE_SLUG} - dashboard page
        do_action('before_dashboard_page_header_section_' . $current_page['page_slug']);
        // HOOK: Action - before_dashboard_page_title_{PLUGIN_SLUG} - All dashboard page
        do_action('before_dashboard_page_title_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_title_{PAGE_SLUG} - dashboard page
        do_action('before_dashboard_page_title_' . $current_page['page_slug']);
        echo '<h1 class="wp-heading-inline">' . $this->plugin_options->get('plugin_name') . ' | ' . $current_page['page_title'] . '</h2>';
        // HOOK: Action - after_dashboard_page_title_{PLUGIN_SLUG} - All dashboard page
        do_action('after_dashboard_page_title_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_title_{PAGE_SLUG} - dashboard page
        do_action('after_dashboard_page_title_' . $current_page['page_slug']);
        if(isset($current_page['page_description']) && ($current_page['page_description'] != null || $current_page['page_description'] != ''))
        {
            echo '<p><i>' . $current_page['page_description'] . '</i></p>';
        }
        // HOOK: Action - after_dashboard_page_header_section_{PLUGIN_SLUG} - All dashboard page
        do_action('after_dashboard_page_header_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_header_section_{PAGE_SLUG} - dashboard page
        do_action('after_dashboard_page_header_section_' . $current_page['page_slug']);
        echo '<hr class="wp-header-end">';
    }

    public function render_tabs() : void
    {
        $current_page = $this->get_current_page();
        $current_page_tabs = $this->get_current_page_tabs($current_page);
        $active_tab = $this->get_active_tab($current_page_tabs);
        // HOOK: Action - before_tabs_render_{PAGE_SLUG}
        do_action('before_tabs_render_' . $current_page['page_slug']);
        echo '<h2 class="nav-tab-wrapper">';
        foreach($current_page_tabs as $current_tab)
        {
            // TODO: check tab is enabled or not, and then render it accordingly.
            echo '<a href="?page=' . $current_tab['tab_parent_page_slug'] . '&tab=' . $current_tab['tab_slug'] . '" style="display:inline-block;vertical-align:middle;" 
            class="nav-tab ' . (($current_tab['tab_slug'] == $active_tab['tab_slug']) ? 'nav-tab-active' : '') . '">';
            // HOOK: Action - before_tab_title_render_{TAB_SLUG}
            do_action('before_tab_title_render_' . $current_tab['tab_slug'], $current_tab, $active_tab);
            echo $current_tab['tab_title'];
            // HOOK: Action - after_tab_title_render_{TAB_SLUG}
            do_action('after_tab_title_render_' . $current_tab['tab_slug'], $current_tab, $active_tab);
            echo '</a>';
        }
        echo '</h2>';
        foreach($current_page_tabs as $current_tab)
        {
            if($current_tab['tab_slug'] == $active_tab['tab_slug'])
            {
                echo '<p>' . $current_tab['tab_description'] . '</p>';
                echo '<dev class="' . $this->plugin_options->get('plugin_slug') . '-tab-content-wrapper" ' . $current_tab['tab_slug'] . '-tab-content-wrapper">';
                // HOOK: Action - before_tab_content_render_{TAB_SLUG}
                do_action('before_tab_content_render_' . $current_tab['tab_slug'], $current_tab);
                $this->render_sections($current_page, $current_tab);
                // HOOK: Action - after_tab_content_render_{TAB_SLUG}
                do_action('after_tab_content_render_' . $current_tab['tab_slug'], $current_tab);
                echo '<div>';
            }
        }
        // echo '<div>';
        // HOOK: Action - after_tabs_render_{PAGE_SLUG}
        do_action('after_tabs_render_' . $current_page['page_slug']);
    }

    public function render_sections(array $current_page, array $parent_tab = []) : void
    {
        global $wp_settings_fields;
        $current_page_sections = $this->get_registered_page_sections($current_page);
        if($current_page_sections)
        {
            $section_parent_tab_slug = empty($parent_tab) ? $current_page['page_slug'] : $parent_tab['tab_slug'];
            foreach($current_page_sections as $section)
            {
                if($section['section_parent_tab_slug'] == $section_parent_tab_slug)
                {
                    echo '<form method="post" action="options.php">';
                    settings_fields($section['section_slug']);
                    $this->render_section_header($section);
                    if(
                        !isset($wp_settings_fields) ||
                        !isset($wp_settings_fields[$current_page['page_slug']]) ||
                        !isset($wp_settings_fields[$current_page['page_slug']][$section['section_slug']])
                    )
                    {
                        continue;
                    }
                    echo '<table class="form-table" role="presentation">';
                    $this->render_option_fields($current_page, $section);
                    echo '</table>';
                    submit_button('Save ' . $section['section_title'] . ' Settings');
                    echo '</form><div class="clear"></div>';
                }
            }
        }
    }

    public function render_section_header(array $section) : void
    {
        echo '<h2>';
        // HOOK: Action - before_section_title_render_{SECTION_SLUG}
        do_action('before_section_title_render_' . $section['section_slug']);
        echo $section['section_title'];
        // HOOK: Action - after_section_title_render_{SECTION_SLUG}
        do_action('after_section_title_render_' . $section['section_slug']);
        echo '</h2>';
        if(isset($section['section_ui']))
        {
            \is_array($section['section_ui']) ? call_user_func($section['section_ui'], $section) : null;
            \is_file($section['section_ui']) ? include_once $section['section_ui'] : null;
            \is_string($section['section_ui']) ? printf($section['section_ui']) : null;
        }
        else
        {
            echo '<p>' . $section['section_description'] . '</p>';
        }
    }


    public function render_option_fields(array $current_page, array $parent_section = []) : void
    {
        global $wp_settings_fields;

        $current_page_sections = $this->get_registered_page_sections($current_page);
        if($current_page_sections)
        {
            $section_parent_tab_slug = empty($parent_tab) ? $current_page['page_slug'] : $parent_tab['tab_slug'];



        }
        // if(!isset($wp_settings_fields[$current_page['page_slug']][$parent_section['section_slug']]))
        // {
        //     return;
        // }







        foreach($wp_settings_fields[$current_page['page_slug']][$parent_section['section_slug']] as $field)
        {


            // HOOK: Filter before_option_render_{OPTION_SLUG}
            $field['args'] = apply_filters('before_option_render_' . $field[ 'args' ][ 'option_slug' ] , $field[ 'args' ] );

            $class = '';
            if( ! empty( $field[ 'args' ][ 'option_class' ] ) )
            {
                $class = 'class="' . esc_attr( $field[ 'args' ][ 'option_class' ] ) . '" valign="top"';
            }
            echo '<tr ' . $class . '><th scope="row"><div style="display:inline-block;vertical-align:top;">';
            echo '<label for="' . esc_attr( $field[ 'args' ][ 'option_label_for' ] ) . '" style="vertical-align:baseline;">' . $field[ 'title' ] . '</label>';
            if ( isset( $field[ 'args' ][ 'option_help_message' ] ) )
            {
                $icon_style = ( isset( $field[ 'args' ][ 'option_help_icon_style' ] ) ? $field[ 'args' ][ 'option_help_icon_style' ] : '' );
                $icon = ( isset( $field[ 'args' ][ 'option_help_icon' ] ) ? $field[ 'args' ][ 'option_help_icon' ] : 'dashicons dashicons-editor-help' );
                echo '<div class="pluginpress_tooltip"><span style="vertical-align:baseline;margin-left:5px;' . $icon_style .'">';
                echo '<i class="' . $icon . '" aria-hidden="true"></i></span><span class="pluginpress_tooltip_text">' .
                $field[ 'args' ][ 'option_help_message' ] . '</span>';
                echo '</div>';
            }
            echo '</div></th><td>';
            call_user_func( $field[ 'callback' ], $field[ 'args' ] );
            echo '</td></tr>';
        }
    }












    public function render_sections_and_fields(array $current_page, array $active_tab = []) : void
    {
        global $wp_settings_sections, $wp_settings_fields;
        if(!isset($wp_settings_sections[$current_page['page_slug']]))
        {
            return;
        }
        foreach($this->sections as $section)
        {

            if(!$active_tab['tab_slug'] == $section['section_parent_tab_slug'])
            {
                continue;
            }

            foreach((array) $wp_settings_sections[$current_page['page_slug']] as $registered_section)
            {
                if($section['section_slug'] == $registered_section['id'])
                {
                    // HOOK: Filter before_section_render_{SECTION_SLUG}
                    $section = apply_filters('before_section_render_' . $section['section_slug'] , $section);
                    // TODO: $section['section_enabled'] == false ? continue; : null;

                    echo '<h2>';
                    // HOOK: Action - before_section_title_render_{SECTION_SLUG}
                    do_action('before_section_title_render_' . $section['section_slug']);
                    echo $section['section_title'];
                    // HOOK: Action - after_section_title_render_{SECTION_SLUG}
                    do_action('after_section_title_render_' . $section['section_slug']);
                    echo '</h2>';
                    if(isset($section['section_ui']))
                    {
                        \is_array($section['section_ui']) ? call_user_func($section['section_ui'], $section) : null;
                        \is_file($section['section_ui']) ? include_once $section['section_ui'] : null;
                        \is_string($section['section_ui']) ? printf($section['section_ui']) : null;
                    }
                    else
                    {
                        echo '<p>' . $section['section_description'] . '</p>';
                    }
                    if(
                        !isset($wp_settings_fields) ||
                        !isset($wp_settings_fields[$current_page['page_slug']]) ||
                        !isset($wp_settings_fields[$current_page['page_slug']][$section['section_slug']])
                    )
                    {
                        continue;
                    }
                    echo '<table class="form-table" role="presentation">';
                    $this->render_settings_fields($current_page['page_slug'], $section['section_slug']);
                    echo '</table>';
                }
            }

        }
    }

    // public function render_sections(array $current_page, array $active_tab = []) : void
    // {
    //     echo '<form method="post" action="options.php">';
    //     settings_fields($active_tab['tab_slug']);
    //     $this->render_sections_and_fields(current_page : $current_page, active_tab : $active_tab);
    //     submit_button();
    //     echo '</form>';
    // }



    public function render_settings_fields($page_slug, $section_slug) : void
    {
        global $wp_settings_fields;

        if(!isset($wp_settings_fields[$page_slug][$section_slug]))
        {
            return;
        }
        foreach((array) $wp_settings_fields[$page_slug][$section_slug] as $field)
        {
        //             print('<pre>');
        // var_dump($field);
        // print('</pre>');
        // die;

            // HOOK: Filter before_option_render_{OPTION_SLUG}
            $field['args'] = apply_filters('before_option_render_' . $field[ 'args' ][ 'option_slug' ] , $field[ 'args' ] );

            $class = '';
            if( ! empty( $field[ 'args' ][ 'option_class' ] ) )
            {
                $class = 'class="' . esc_attr( $field[ 'args' ][ 'option_class' ] ) . '" valign="top"';
            }
            echo '<tr ' . $class . '><th scope="row"><div style="display:inline-block;vertical-align:top;">';
            echo '<label for="' . esc_attr( $field[ 'args' ][ 'option_label_for' ] ) . '" style="vertical-align:baseline;">' . $field[ 'title' ] . '</label>';
            if ( isset( $field[ 'args' ][ 'option_help_message' ] ) )
            {
                $icon_style = ( isset( $field[ 'args' ][ 'option_help_icon_style' ] ) ? $field[ 'args' ][ 'option_help_icon_style' ] : '' );
                $icon = ( isset( $field[ 'args' ][ 'option_help_icon' ] ) ? $field[ 'args' ][ 'option_help_icon' ] : 'dashicons dashicons-editor-help' );
                echo '<div class="pluginpress_tooltip"><span style="vertical-align:baseline;margin-left:5px;' . $icon_style .'">';
                echo '<i class="' . $icon . '" aria-hidden="true"></i></span><span class="pluginpress_tooltip_text">' .
                $field[ 'args' ][ 'option_help_message' ] . '</span>';
                echo '</div>';
            }
            echo '</div></th><td>';
            call_user_func( $field[ 'callback' ], $field[ 'args' ] );
            echo '</td></tr>';
        }
    }


    public function render_options( $args ) : void
    {
        echo UI::get( $args );
        echo '<p>' . $args[ 'option_description' ] . '</p>';
    }

















    public function render_page_footer_section(array $current_page) : void
    {
        // HOOK: Action - before_dashboard_page_footer_section_{PLUGIN_SLUG} - All Admin page
        do_action('before_dashboard_page_footer_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_footer_section_{PAGE_SLUG} - Admin page
        do_action('before_dashboard_page_footer_section_' . $current_page['page_slug']);
        if(isset($current_page['page_footer']) && ($current_page['page_footer'] != null || $current_page['page_footer'] != ''))
        {
            if(is_file($current_page['page_footer']))
            {
                echo '<div  id="' . $current_page['page_slug'].'-footer" role="contentinfo">';
                include_once $current_page['page_footer'];
                echo '</div>';
            }
            else
            {
                echo '<div id="' . $current_page['page_slug'].'-footer" role="contentinfo">';
                echo $current_page['page_footer'];
                echo '</div>';
            }
        }
        else
        {
            echo
            <<<PAGE_FOOTER
                <div id="{$current_page['page_slug']}-footer" role="contentinfo">
                    <i>
                        <p id="{$current_page['page_slug']}-footer-left" class="alignleft">
                            <span id="{$current_page['page_slug']}-footer-thankyou">
                                Thank you for using <a href="{$this->plugin_options->get('plugin_url')}" title="{$this->plugin_options->get('plugin_name')}">
                                {$this->plugin_options->get('plugin_name')}</a>. - Please consider leaving your valued <a href="
                                {$this->plugin_options->get('plugin_feedback_url')}" title="{$this->plugin_options->get('plugin_name')}">feedback <a href="
                                {$this->plugin_options->get('plugin_feedback_url')}" title="{$this->plugin_options->get('plugin_name')}" style="color:#D97D0D;">
                                <span class="dashicons-before dashicons-star-half"><span class="dashicons-before dashicons-star-half"><span class="dashicons-before dashicons-star-half"><span class="dashicons-before dashicons-star-half"><span class="dashicons-before dashicons-star-half">
                                </a>
                            </span>
                        </p>
                    </i>
                    <p id="footer-upgrade" class="alignright">Version {$this->plugin_options->get('plugin_version')}</p>
                    <div class="clear"></div>
                </div>
            PAGE_FOOTER;
        }
        // HOOK: Action - after_dashboard_page_footer_section_{PLUGIN_SLUG} - All Admin page
        do_action('after_dashboard_page_footer_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_footer_section_{PAGE_SLUG} - Admin page
        do_action('after_dashboard_page_footer_section_' . $current_page['page_slug']);
    }
}