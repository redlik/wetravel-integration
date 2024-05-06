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
        <div class="p-4">
            <h2 class="mb-4 fw-bold">WeTravel Integration</h2>

        <form action="" method="POST">
            <input type="hidden" name="action" value="save_wetravel_api_key" />
            <?php wp_nonce_field('wetravel_integration_nonce'); ?>
            <div class="mb-2">
                <label for="api_key" class="form-label fw-bold">API Key:</label>
                <textarea name="api_key" id="api_key" cols="80" rows="5" class="form-control bg-white"><?php echo $api_key; ?>
                </textarea>
            </div>
        <input type="submit" value="Save Key" class="btn btn-primary my-2">
        </form>
        <hr>

        </div>
    <?php
    }
}
