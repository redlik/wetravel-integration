<?php

namespace WetravelIntegration\Admin;

class AdminPanel
{
    public function create_admin_menu()
    {
        return add_options_page(
            'We travel Integration',
            'WeTravel',
            'manage_options',
            'wetravel-integration',
            array($this,'wetravel_integration_option_page')
        );
    }

    public function wetravel_integration_option_page()
    {
        $api_key = get_option('wetravel_api_key');
        
        ?>
        <div >
            <h2>WeTravel Integration</h2>
        </div>
        <form action="" method="POST">
            <input type="hidden" name="action" value="save_wetravel_api_key" />
            <?php wp_nonce_field('wetravel_integration_nonce'); ?>
            <div>
            API Key:<br/>
                <textarea name="api_key" id="api_key" cols="80" rows="12">
                    <?php echo $api_key; ?>
                </textarea>
            </div>
        </form>
        <input type="submit" value="Save Key" class="btn btn-primary mt-2">
    <?php
    }
}
