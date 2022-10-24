<?php

namespace PluginPress\PluginPressAPI\Admin;

use PluginPress\PluginPressAPI\UI\UI;

// If this file is called directly, abort. for the security purpose.
if( ! defined( 'WPINC' ) )
{
    die;
}

trait AdminSettingsUI
{
    public function render_sections( $args ) : void
    {
        if( isset( $this->sections ) && ! empty( $this->sections ) )
        {
            foreach( $this->sections as $section )
            {
                if( isset( $section[ 'section_description' ] ) && ( $args[ 'section_slug' ] == $section[ 'section_slug' ] ) )
                {
                    echo '<p>' . $section[ 'section_description' ] . '</p>';
                }
            }
        }
    }
}