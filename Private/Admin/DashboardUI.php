<?php

namespace PluginPress\PluginPressAPI\Admin;

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
        if(!$current_page['page_ui_template'] == '')
        {
            include_once $current_page['page_ui_template'];
            return;
        }
        echo '<div class="wrap">';
        $this->render_page_header_section($current_page);
        if(!empty($this->tabs))
        {
            $page_tabs = $this->get_current_page_tabs($current_page);
            $active_tab = $this->get_active_tab($page_tabs);
            if($active_tab)
            {
                $this->render_tabs($page_tabs);
                echo '<form method="post" action="options.php">';
                settings_fields($active_tab['tab_slug']);
                $this->render_sections_and_fields($current_page, $active_tab);
                submit_button();
                echo '</form>';
            }
        }
        else
        {
            //TODO: render default setting section and fields
            // $this->render_sections();
        }
        $this->render_page_footer_section($current_page);
        echo '</div>';
    }

    public function render_page_header_section($current_page) : void
    {
        // HOOK: Action - before_dashboard_page_header_section_{PLUGIN_SLUG} - All Admin page
        do_action('before_dashboard_page_header_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - before_dashboard_page_header_section_{PAGE_SLUG} - Admin page
        do_action('before_dashboard_page_header_section_' . $current_page['page_slug']);

        // TODO: Implement the icon section
        echo '<h1 class="wp-heading-inline">' . $this->plugin_options->get('plugin_name') . ' | ' . $current_page['page_title'] . '</h1>';

        // HOOK: Action - after_dashboard_page_title_{PLUGIN_SLUG} - All Admin page
        do_action('after_dashboard_page_title_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_title_{PAGE_SLUG} - Admin page
        do_action('after_dashboard_page_title_' . $current_page['page_slug']);
        if(isset($current_page['page_description']) && ($current_page['page_description'] != null || $current_page['page_description'] != ''))
        {
            echo '<p><i>' . $current_page['page_description'] . '</i></p>';
        }
        // HOOK: Action - after_dashboard_page_header_section_{PLUGIN_SLUG} - All Admin page
        do_action('after_dashboard_page_header_section_' . $this->plugin_options->get('plugin_slug'));
        // HOOK: Action - after_dashboard_page_header_section_{PAGE_SLUG} - Admin page
        do_action('after_dashboard_page_header_section_' . $current_page['page_slug']);
        echo '<hr class="wp-header-end">';
    }

    public function render_page_footer_section($current_page) : void
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