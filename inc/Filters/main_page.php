<?php

if ( ! defined( 'WPINC' ) ) { die( "Don't mess with us." ); }
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Remove deprecated objects from the table
 *
 * @since    8.0.0
 **/
add_filter('cfgp/table/shortcodes', 'cfgp_admin_main_page_table_shortcodes');
add_filter('cfgp/table/simple_shortcodes', 'cfgp_admin_main_page_table_shortcodes');
add_filter('cfgp/table/tags', 'cfgp_admin_main_page_table_shortcodes');
if( !function_exists('cfgp_admin_main_page_table_shortcodes') ) {
	function cfgp_admin_main_page_table_shortcodes($API){
		
		if(isset($API['timezoneName'])){
			unset($API['timezoneName']);
		}
		
		if(isset($API['state'])){
			unset($API['state']);
		}
		
		return $API;
	}
}

/**
 * Special dedicated short codes
 *
 * @since    8.0.0
 **/
add_action('cfgp/table/after/shortcodes', function($API){ ?>
<tr>
	<th colspan="2">
		<h3><?php _e('Special dedicated short codes', CFGP_NAME); ?></h3>
	</th>
</tr>
<tr>
    <td><code>[cfgeo_is_not_vat]<?php _e('You are NOT under VAT', CFGP_NAME); ?>[/cfgeo_is_not_vat]</code></td>
    <td><?php echo do_shortcode('[cfgeo_is_not_vat]' .__('You are NOT under VAT', CFGP_NAME). '[/cfgeo_is_not_vat]'); ?></td>
</tr>
<tr>
    <td><code>[cfgeo_in_eu]<?php _e('You are from the EU', CFGP_NAME); ?>[/cfgeo_in_eu]</code></td>
    <td><?php echo do_shortcode('[cfgeo_in_eu]' .__('You are from the EU', CFGP_NAME). '[/cfgeo_in_eu]'); ?></td>
</tr>
<tr>
    <td><code>[cfgeo_not_in_eu]<?php _e('You are NOT from the EU', CFGP_NAME); ?>[/cfgeo_not_in_eu]</code></td>
    <td><?php echo do_shortcode('[cfgeo_not_in_eu]' .__('You are NOT from the EU', CFGP_NAME). '[/cfgeo_not_in_eu]'); ?></td>
</tr>
<tr>
    <td><kbd>[cfgeo_gps]<?php _e('GPS is enabled', CFGP_NAME); ?>[/cfgeo_gps]</kbd></td>
    <td>
        <?php echo do_shortcode('[cfgeo_gps]' .__('GPS is enabled.', CFGP_NAME). '[/cfgeo_gps]'); ?> 
        <span class="badge"><?php
            if(CFGP_U::is_plugin_active('cf-geoplugin-gps/cf-geoplugin-gps.php'))
            {
                echo do_shortcode('[cfgeo_gps default="' .__('GPS is NOT enabled', CFGP_NAME). '"]' .__('GPS is enabled', CFGP_NAME). '[/cfgeo_gps]');
            }
            else
            {
                printf( 
                    sprintf(
                        ' ' . __('GPS is enabled only with %s extension', CFGP_NAME),
                        sprintf(
                            '<a href="%1$s" class="thickbox open-plugin-details-modal" target="_blank">' . __('CF Geo Plugin GPS', CFGP_NAME) . '</a>',
                            CFGP_U::admin_url('plugin-install.php?tab=plugin-information&plugin=cf-geoplugin-gps&TB_iframe=true&width=772&height=923')
                        )
                    )
                );
            }
        ?></span>
    </td>
</tr>
<tr>
    <td><kbd>[cfgeo_is_proxy]<?php _e('Proxy connection', CFGP_NAME); ?>[/cfgeo_is_proxy]</kbd></td>
    <td><?php echo do_shortcode('[cfgeo_is_proxy]' .__('Proxy connection', CFGP_NAME). '[/cfgeo_is_proxy]'); ?></td>
</tr>
<tr>
    <td><kbd>[cfgeo_is_not_proxy]<?php _e('Is not proxy connection', CFGP_NAME); ?>[/cfgeo_is_not_proxy]</kbd></td>
    <td><?php echo do_shortcode('[cfgeo_is_not_proxy]' .__('Is not proxy connection', CFGP_NAME). '[/cfgeo_is_not_proxy]'); ?></td>
</tr>
<tr>
    <td><kbd>[cfgeo_converter from="<?php echo CFGP_Options::get('base_currency'); ?>" to="<?php echo CFGP_U::api('currency'); ?>"]10[/cfgeo_converter]</kbd></td>
    <td><?php echo do_shortcode('[cfgeo_converter from="' . CFGP_Options::get('base_currency') . '" to="' . CFGP_U::api('currency') . '"]10[/cfgeo_converter]'); ?></td>
</tr>
<?php }, 30);

/**
 * Special dedicated simple short codes
 *
 * @since    8.0.0
 **/
add_action('cfgp/table/after/simple_shortcodes', function($API){ ?>
<tr>
	<th colspan="2">
		<h3><?php _e('Special dedicated short codes', CFGP_NAME); ?></h3>
	</th>
</tr>
<tr>
    <td><code>[is_not_vat]<?php _e('You are NOT under VAT', CFGP_NAME); ?>[/is_not_vat]</code></td>
    <td><?php echo do_shortcode('[is_not_vat]' .__('You are NOT under VAT', CFGP_NAME). '[/is_not_vat]'); ?></td>
</tr>
<tr>
    <td><code>[in_eu]<?php _e('You are from the EU', CFGP_NAME); ?>[/in_eu]</code></td>
    <td><?php echo do_shortcode('[in_eu]' .__('You are from the EU', CFGP_NAME). '[/in_eu]'); ?></td>
</tr>
<tr>
    <td><code>[not_in_eu]<?php _e('You are NOT from the EU', CFGP_NAME); ?>[/not_in_eu]</code></td>
    <td><?php echo do_shortcode('[not_in_eu]' .__('You are NOT from the EU', CFGP_NAME). '[/not_in_eu]'); ?></td>
</tr>
<tr>
    <td><kbd>[gps]<?php _e('GPS is enabled', CFGP_NAME); ?>[/gps]</kbd></td>
    <td>
        <?php echo do_shortcode('[gps]' .__('GPS is enabled.', CFGP_NAME). '[/gps]'); ?> 
        <span class="badge"><?php
            if(CFGP_U::is_plugin_active('cf-geoplugin-gps/cf-geoplugin-gps.php'))
            {
                echo do_shortcode('[gps default="' .__('GPS is NOT enabled', CFGP_NAME). '"]' .__('GPS is enabled', CFGP_NAME). '[/gps]');
            }
            else
            {
                printf( 
                    sprintf(
                        ' ' . __('GPS is enabled only with %s extension', CFGP_NAME),
                        sprintf(
                            '<a href="%1$s" class="thickbox open-plugin-details-modal" target="_blank">' . __('CF Geo Plugin GPS', CFGP_NAME) . '</a>',
                            CFGP_U::admin_url('plugin-install.php?tab=plugin-information&plugin=cf-geoplugin-gps&TB_iframe=true&width=772&height=923')
                        )
                    )
                );
            }
        ?></span>
    </td>
</tr>
<tr>
    <td><kbd>[is_proxy]<?php _e('Proxy connection', CFGP_NAME); ?>[/is_proxy]</kbd></td>
    <td><?php echo do_shortcode('[is_proxy]' .__('Proxy connection', CFGP_NAME). '[/is_proxy]'); ?></td>
</tr>
<tr>
    <td><kbd>[is_not_proxy]<?php _e('Is not proxy connection', CFGP_NAME); ?>[/is_not_proxy]</kbd></td>
    <td><?php echo do_shortcode('[is_not_proxy]' .__('Is not proxy connection', CFGP_NAME). '[/is_not_proxy]'); ?></td>
</tr>
<?php }, 30);