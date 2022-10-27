<?php

namespace PluginPress\PluginPressAPI\WordPress;

use PluginPress\PluginPressAPI\PluginOptions\PluginOptions;

// If this file is called directly, abort. for the security purpose.
if(!defined('WPINC'))
{
    die('Unauthorized access..!');
}

class PluginsPageCustomizer
{
    public function __construct(private PluginOptions $plugin_options)
    {
        $this->init();
    }

    public function init() : void
    {
        add_filter('plugin_action_links_' . $this->plugin_options->get('plugin_base_name'), [$this, 'render_plugins_page_links']);
        add_filter('plugin_row_meta', [$this, 'render_plugin_row_meta_links'], 10, 2);
        add_action('in_plugin_update_message-' . $this->plugin_options->get('plugin_base_name'), [$this, 'render_plugin_update_message'], 10, 2);
    }

    public function render_plugins_page_links(array $links) : array
    {
        if($this->plugin_options->get('plugin_settings_url') != false)
        {
            $settings_link = '<a href="' . $this->plugin_options->get('plugin_settings_url') .
                '"><span class="dashicons-before dashicons-admin-generic"></span>Settings</a>';
            array_push($links, $settings_link);
        }
        if($this->plugin_options->get('plugin_support_url') != false)
        {
            $support_link = '<a href="' . $this->plugin_options->get('plugin_support_url') .
                '" target="_blank" style="color:#2B8C69;"><span class="dashicons-before dashicons-sos"></span>Support</a>';
            array_push($links, $support_link);
        }
        if($this->plugin_options->get('plugin_feedback_url') != false)
        {
            $leave_feedback_link = '<a href="' . $this->plugin_options->get('plugin_feedback_url') .
                '" target="_blank" style="color:#D97D0D;"><span class="dashicons-before dashicons-star-half"></span>Feedback</a>';
            array_push($links, $leave_feedback_link);
        }
        return $links;
    }

    public function render_plugin_row_meta_links(array $meta_links, string $file) : array
    {
        if($this->plugin_options->get('plugin_base_name') == $file)
        {
            $social_links = [];
            if($this->plugin_options->get('plugin_social_urls') != false)
            {
                foreach($this->plugin_options->get('plugin_social_urls') as $profile)
                {
                    if(!isset($profile[ 'link' ]) || $profile['link'] == '' || $profile['link'] == null)
                    {
                        continue;
                    }
                    else
                    {
                        isset($profile['name'])     ? $profile_name = $profile['name']      : $profile_name = rand(2, 12);
                        isset($profile['title'])    ? $profile_title = $profile['title']    : $profile_title = 'Link';
                        isset($profile['color'])    ? $profile_color = $profile['color']    : $profile_color = '#D97D0D';
                        isset($profile['icon'])     ? $profile_icon = $profile['icon']      : $profile_icon = 'dashicons-before dashicons-admin-generic';
                        $social_links = array_merge(
                            $social_links,
                            [
                                $profile_name => '<a href="' . $profile['link'] . '" target="_blank" style="color:' . $profile_color . ';"><span class="'
                                    . $profile_icon . '"></span>' . $profile_title . '</a>',
                            ]
                        );
                    }
                }
            }
            return array_merge($meta_links, $social_links);
        }
        return $meta_links;
    }

    public function render_plugin_update_message($plugin_data, $response) : void
    {
        if($this->plugin_options->get('plugin_update_notice_url') != false)
        {
            $curl = curl_init($this->plugin_options->get('plugin_update_notice_url'));
            $_user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_7) AppleWebKit/534.48.3 (KHTML, like Gecko) Version/5.1 Safari/534.48.3';
            curl_setopt($curl, CURLOPT_USERAGENT, $_user_agent);
            curl_setopt($curl, CURLOPT_FAILONERROR, true);
            $update_notice = strval(curl_exec($curl));
            if(curl_errno($curl))
            {
                $error_message = curl_error($curl);
                error_log(
                    esc_html__(
                        'ERROR: ' . $this->plugin_options->get('plugin_name') . ' cannot show plugin update message. | ' . $error_message,
                        $this->plugin_options->get('plugin_text_domain')
                    )
                );
            }
            else
            {
                ob_start();
                // TODO: Customize the output to be more visual and user friendly.
                echo $update_notice;
                ob_clean();
            }
            curl_close($curl);
        }
    }
}