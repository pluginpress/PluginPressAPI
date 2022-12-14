<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\UI\UI;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die('Unauthorized access..!');
}

trait DashboardUI
{
    public function render_dashboard_page_ui() : void
    {
        $current_page = $this->get_current_page();
        if(!$current_page['page_ui_template'] == '' && \is_file($current_page['page_ui_template']))
        {
            include_once $current_page['page_ui_template'];
            return;
        }
        echo '<div class="wrap" style="">';
        $this->render_page_header_section(current_page : $current_page);
        echo settings_errors();
        $current_page_tabs = $this->get_current_page_tabs(current_page : $current_page);
        if($current_page_tabs)
        {
            $active_tab = $this->get_active_tab(page_tabs : $current_page_tabs);
            if($active_tab)
            {
                $this->render_tabs();
            }
        }
        $this->render_sections(parent : $current_page['page_slug']);
        $this->render_page_footer_section(current_page : $current_page);
        echo '<div class="clear"></div>';
        echo '</div>';
    }

    public function render_header_enqueued_scripts() : void
    {
        $current_page = $this->get_current_page();
        if(
            isset($current_page['enqueue_on_page_head']) &&
            !empty($current_page['enqueue_on_page_head']) &&
            is_array($current_page['enqueue_on_page_head'])
        )
        {
            foreach($current_page['enqueue_on_page_head'] as $script)
            {
                if($script['enqueue_location'] == 'bottom')
                {
                    continue;
                }
                if($script['enqueue_script_type'] == 'style')
                {
                    echo '<link id="' . $script['enqueue_script_slug'] . '" href="' . $script['enqueue_script_path'] . '" rel="stylesheet" media="all" />';
                }
                if($script['enqueue_script_type'] == 'script')
                {
                    echo '<script id="' . $script['enqueue_script_slug'] . '" src="' . $script['enqueue_script_path'] . '"></script>';
                }
            }
        }
    }

    public function render_footer_enqueued_scripts() : void
    {
        $current_page = $this->get_current_page();
        if(
            isset($current_page['enqueue_on_page_footer']) &&
            !empty($current_page['enqueue_on_page_footer']) &&
            is_array($current_page['enqueue_on_page_footer'])
        )
        {
            foreach($current_page['enqueue_on_page_footer'] as $script)
            {
                if($script['enqueue_location'] == 'top')
                {
                    continue;
                }
                if($script['enqueue_script_type'] == 'style')
                {
                    echo '<link id="' . $script['enqueue_script_slug'] . '" href="' . $script['enqueue_script_path'] . '" rel="stylesheet" media="all" />';
                }
                if($script['enqueue_script_type'] == 'script')
                {
                    echo '<script id="' . $script['enqueue_script_slug'] . '" src="' . $script['enqueue_script_path'] . '"></script>';
                }
            }
        }
    }

    public function render_page_header_section(array $current_page) : void
    {
        // HOOK: Action - before_dashboard_page_header_section_{PLUGIN_SLUG} - All dashboard page
        do_action('before_dashboard_page_header_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_header_section_{PAGE_SLUG} - dashboard page
        do_action('before_dashboard_page_header_section_' . $current_page['page_slug']);
        echo '<div class="" style="">';
        echo '<div class="" style="padding:5px;">';
        // HOOK: Action - before_dashboard_page_title_{PLUGIN_SLUG} - All dashboard page
        do_action('before_dashboard_page_title_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_title_{PAGE_SLUG} - dashboard page
        do_action('before_dashboard_page_title_' . $current_page['page_slug']);
        // HOOK: Filter - dashboard_page_title_{PAGE_SLUG} - dashboard page
        $page_title = '<h1 class="wp-heading-inline">' . $this->plugin_options->get('plugin_name') . ' | ' . $current_page['page_title'] . '</h2>';
        echo apply_filters('dashboard_page_title_' . $current_page['page_slug'], $page_title);
        // HOOK: Action - after_dashboard_page_title_{PLUGIN_SLUG} - All dashboard page
        do_action('after_dashboard_page_title_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_title_{PAGE_SLUG} - dashboard page
        do_action('after_dashboard_page_title_' . $current_page['page_slug']);
        echo '<div class="clear"></div>';
        echo '</div>';
        if(isset($current_page['page_description']) && ($current_page['page_description'] != null || $current_page['page_description'] != ''))
        {
            echo '<p style="padding:5px;"><i>' . $current_page['page_description'] . '</i></p>';
        }
        echo '<div class="clear"></div>';
        echo '</div>';
        // HOOK: Action - after_dashboard_page_header_section_{PLUGIN_SLUG} - All dashboard page
        do_action('after_dashboard_page_header_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_header_section_{PAGE_SLUG} - dashboard page
        do_action('after_dashboard_page_header_section_' . $current_page['page_slug']);
        echo '<hr class="wp-header-end">';
    }

    public function render_tabs() : void
    {
        $current_page       = $this->get_current_page();
        $current_page_tabs  = $this->get_current_page_tabs(current_page : $current_page);
        $active_tab         = $this->get_active_tab(page_tabs : $current_page_tabs);
        // HOOK: Action - before_tabs_render_{PAGE_SLUG}
        do_action('before_tabs_render_' . $current_page['page_slug']);
        echo '<div class="" style="border:1px solid #c1c1c1;margin:20px 0px;">';
        echo '<h2 class="nav-tab-wrapper">';
        foreach($current_page_tabs as $current_tab)
        {
            // TODO: check tab is enabled or not, and then render it accordingly.
            echo '<a href="?page=' . $current_tab['tab_parent_page_slug'] . '&tab=' . $current_tab['tab_slug'] .
                '" style="display:inline-block;vertical-align:middle;" class="nav-tab ' .
                (($current_tab['tab_slug'] == $active_tab['tab_slug']) ? 'nav-tab-active' : '') . '">';
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
                echo '<p style="padding:5px;">' . $current_tab['tab_description'] . '</p>';
                echo '<div class="" style="border-top:1px solid #c1c1c1; padding:5px;">';
                // HOOK: Action - before_tab_content_render_{TAB_SLUG}
                do_action('before_tab_content_render_' . $current_tab['tab_slug'], $current_tab);
                $this->render_tabbed_sections();
                // HOOK: Action - after_tab_content_render_{TAB_SLUG}
                do_action('after_tab_content_render_' . $current_tab['tab_slug'], $current_tab);
                echo '<div class="clear"></div>';
                echo '</div>';
            }
        }
        echo '<div class="clear"></div>';
        echo '</div>';
        // HOOK: Action - after_tabs_render_{PAGE_SLUG}
        do_action('after_tabs_render_' . $current_page['page_slug']);
    }

    public function render_tabbed_sections() : void
    {
        global $wp_settings_sections;
        $current_page = $this->get_current_page();
        if(!isset($wp_settings_sections[$current_page['page_slug']]))
        {
            return;
        }
        $current_page_tabs  = $this->get_current_page_tabs(current_page : $current_page);
        $active_tab         = $this->get_active_tab(page_tabs :  $current_page_tabs);
        $this->render_sections(parent : $active_tab['tab_slug']);
    }

    public function render_sections(string $parent) : void
    {
        //TODO: render sections that don't have tab assigned.
        global $wp_settings_sections, $wp_settings_fields;
        $current_page = $this->get_current_page();
        if(!isset($wp_settings_sections) || !isset($wp_settings_sections[$current_page['page_slug']]))
        {
            return;
        }
        echo '<div class="" style="border:1px solid #c1c1c1; padding:5px;background-color:#D0D2D5;">';
        // HOOK: Action - before_sections_render_{SECTION_GROUP_SLUG} page_slug OR tab_slug
        do_action('before_sections_render_' . $parent);
        echo '<form method="post" action="options.php">';
        settings_fields($parent);
        foreach((array)$wp_settings_sections[$current_page['page_slug']] as $section)
        {
            // HOOK: Action - before_section_render_{SECTION_SLUG}
            do_action('before_section_render_' . $section['section_slug']);
            if(
                !isset($wp_settings_fields) ||
                !isset($wp_settings_fields[$current_page['page_slug']]) ||
                !isset($wp_settings_fields[$current_page['page_slug']][$section['id']])
            )
            {
                continue;
            }
            if($section['section_parent_tab_slug'] == $parent)
            {
                // TODO: $section['section_enabled'] == false ? continue; : null;
                echo '<div class="" style="margin:10px 0px; padding:5px;">';
                // HOOK: Action - before_section_header_render_{SECTION_SLUG}
                do_action('before_section_header_render_' . $section['section_slug']);
                echo '<h2>';
                // HOOK: Action - before_section_title_render_{SECTION_SLUG}
                do_action('before_section_title_render_' . $section['section_slug']);
                echo $section['section_title'];
                // HOOK: Action - after_section_title_render_{SECTION_SLUG}
                do_action('after_section_title_render_' . $section['section_slug']);
                echo '</h2>';
                if($section['callback'])
                {
                    call_user_func($section['callback'], $section);
                }
                // HOOK: Action - after_section_header_render_{SECTION_SLUG}
                do_action('after_section_header_render_' . $section['section_slug']);
                $this->render_settings_fields(parent_page : $current_page['page_slug'], parent_section : $section['id']);
                echo '<div class="clear"></div>';
                echo '</div>';
            }
            // HOOK: Action - after_section_render_{SECTION_SLUG}
            do_action('after_section_render_' . $section['section_slug']);
        }
        echo '<div class="" style="margin:0px 10px;">';
        submit_button();
        echo '</div>';
        echo '</form>';
        // HOOK: Action - after_sections_render_{SECTION_GROUP_SLUG} page_slug OR tab_slug
        do_action('after_sections_render_' . $parent);
        echo '<div class="clear"></div>';
        echo '</div>';
    }

    public function render_after_section_header(array $section) : void
    {
        if(!$section['section_ui_template'] == '' && \is_file($section['section_ui_template']))
        {
            include_once $section['section_ui_template'];
            return;
        }
        echo '<p>' . $section['section_description'] . '</p>';
    }

    public function render_settings_fields(string $parent_page, string $parent_section = '') : void
    {
        // TODO: render fields that don't have sections assigned.
        global $wp_settings_fields;
        if(!$parent_section == '' && isset($wp_settings_fields[$parent_page][$parent_section]))
        {
            echo '<table class="form-table" role="presentation">';
            foreach((array)$wp_settings_fields[$parent_page][$parent_section] as $field)
            {
                // HOOK: Filter before_option_render_{OPTION_SLUG}
                $field['args'] = apply_filters('before_option_render_' . $field['args']['option_slug'], $field['args']);
                echo '<tr class="' . esc_attr($field['args']['option_class']) . '" style="' . esc_attr($field['args']['option_style']) . '">';
                echo '<th scope="row">';
                // HOOK: Action - before_option_title_render_{OPTION_SLUG}
                do_action('before_option_title_render_' . $field['args']['option_slug']);
                echo '<label for="' . esc_attr($field['args']['option_label_for']) . '">' . $field['args']['option_title'] . '</label>';
                // HOOK: Action - after_option_title_render_{OPTION_SLUG}
                do_action('after_option_title_render_' . $field['args']['option_slug']);
                echo '</br><small>' . $field['args']['option_description'] . '</small>';
                echo '</th>';
                echo '<td>';
                call_user_func($field['callback'], $field['args']);
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }

    public function render_option_ui(array $options) : void
    {
        echo UI::get_element($options);
    }

    public function render_page_footer_section(array $current_page) : void
    {
        $_stars = '';
        for($i = 0; $i<5; $i++)
        {
            $_stars .= '<span class="dashicons-before dashicons-star-half">';
        }
        // HOOK: Action - before_dashboard_page_footer_section_{PLUGIN_SLUG} - All Admin page
        do_action('before_dashboard_page_footer_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_footer_section_{PAGE_SLUG} - Admin page
        do_action('before_dashboard_page_footer_section_' . $current_page['page_slug']);
        echo '<div class="" style="">';
        echo <<<PAGE_FOOTER
        <div id="{$current_page['page_slug']}-footer" role="contentinfo">
            <i>
                <p id="{$current_page['page_slug']}-footer-left" class="alignleft">
                    <span id="{$current_page['page_slug']}-footer-thankyou">
                        Thank you for using <a href="{$this->plugin_options->get('plugin_url')}" title="{$this->plugin_options->get('plugin_name')}">
                        {$this->plugin_options->get('plugin_name')}</a>. - Please consider leaving your valued <a href="
                        {$this->plugin_options->get('plugin_feedback_url')}" title="{$this->plugin_options->get('plugin_name')}">feedback</a> <a href="
                        {$this->plugin_options->get('plugin_feedback_url')}" title="{$this->plugin_options->get('plugin_name')}" style="color:#D97D0D;
                        ">{$_stars}</a>
                    </span>
                </p>
            </i>
            <p id="footer-upgrade" class="alignright">Version {$this->plugin_options->get('plugin_version')}</p>
            <div class="clear"></div>
        </div>
        PAGE_FOOTER;
        echo '<div class="clear"></div>';
        echo '</div>';
        // HOOK: Action - after_dashboard_page_footer_section_{PLUGIN_SLUG} - All Admin page
        do_action('after_dashboard_page_footer_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_footer_section_{PAGE_SLUG} - Admin page
        do_action('after_dashboard_page_footer_section_' . $current_page['page_slug']);
    }
}