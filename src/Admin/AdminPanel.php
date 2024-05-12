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

        <form action="admin-post.php" method="POST">
            <input type="hidden" name="action" value="save_wetravel_api_key" />
            <?php wp_nonce_field('wetravel_integration_nonce'); ?>
            <div class="mb-2">
                <label for="api_key" class="form-label fw-bold">API Key:</label>
                <textarea
                    name="api_key" id="api_key"
                    cols="80" rows="7"
                    class="form-control bg-white"><?php echo $api_key; ?></textarea>
                <div>
                    <small class="text-muted">Do not change the key unless it is really necessary</small>
                </div>
            </div>
        <input type="submit" value="Save Key" class="btn btn-primary my-2">
        </form>
        <hr>
        <div>
            <p>Current number of active tours: <?php echo(wp_count_posts('tours')->publish) ?></p>
            <p>Numbers of tours scanned by plugin: <?php echo($this->count_scanned_tours()) ?></p>
            <form action="admin-post.php" method="POST">
                <input type="hidden" name="action" value="rescan_tours" />
                <?php wp_nonce_field('wetravel_integration_nonce'); ?>
                <input type="submit" value="Rescan the Tours" class="btn btn-primary my-2">
            </form>
        </div>
        </div>
    <?php
    }

    public function count_scanned_tours()
    {
        if(get_option('wetravel_tours_id')) {
            $tours = get_option('wetravel_tours_id');
            return count($tours);
        } else {
            return 0;
        }


    }
}
