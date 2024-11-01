<?php
/**
 * Settings
 */

if(!defined('ABSPATH')) {
	exit;
}

// Register settings
function uptimerobot_register_settings() {
	register_setting('uptimerobot_settings', 'uptimerobot_apikey', 'trim');
	register_setting('uptimerobot_settings', 'uptimerobot_show_ratio', 'boolval');
	register_setting('uptimerobot_settings', 'uptimerobot_custom_period', 'intval');
	register_setting('uptimerobot_settings', 'uptimerobot_show_psp_link', 'boolval');
	register_setting('uptimerobot_settings', 'uptimerobot_psp_url', 'trim');
}
add_action('admin_init', 'uptimerobot_register_settings');

// Add link to the settings on plugins page
function uptimerobot_plugin_action_links($links) {
	$links[] = '<a href="options-general.php?page=uptime-robot-settings">'.__('Settings', 'uptime-robot-widget').'</a>';
    return $links;
}
add_filter('plugin_action_links_'.UPTIME_ROBOT_WIDGET_BASENAME, 'uptimerobot_plugin_action_links', 10, 2);

// Create options menu
function uptimerobot_add_settings() {
	// Add options page
	if($page_hook = add_options_page('Uptime Robot Widget', 'Uptime Robot Widget', 'manage_options', 'uptime-robot-settings', 'uptimerobot_settings_page')) {
		// Add CSS style
		add_action('admin_head-'.$page_hook, 'uptimerobot_settings_css');
	}
}
add_action('admin_menu', 'uptimerobot_add_settings');

// Add CSS style
function uptimerobot_settings_css() { ?>
	<style>
		.loader{
		animation:spin 0.8s linear infinite;
		}
		@keyframes spin{
		0% { transform: rotate(0deg); }
		100% { transform: rotate(360deg); }
		}
		.uptimerobot-icon{
		cursor:pointer;
		margin-left:2px;
		}
		.uptimerobot-icon:hover{
		opacity:.8;
		}
		.metabox-holder .postbox .hndle{
		cursor:default;
		}
		.rating-stars{
		text-align:center;
		}
		.rating-stars a{
		color:#ffb900;
		text-decoration:none;
		}
	</style>
	<script>
	jQuery(document).ready(function($) {
		$('#uptimerobot_apikey_test').click(function() {
			$.ajax({
				method: 'POST',
				data: 'api_key=' + $('#uptimerobot_apikey').val(),
				url: 'https://api.uptimerobot.com/v2/getAccountDetails',
				success: function(response) {
					if(response.error) {
						$('#uptimerobot_apikey_test').prop('title', response.stat + ', ' + response.error.message);
						$('#uptimerobot_apikey_test').css('color', '#dc3232');
					}
					else {
						$('#uptimerobot_apikey_test').prop('title', response.stat);
						$('#uptimerobot_apikey_test').css('color', '#46b450');
					}
				},
				error: function() {
					$('#uptimerobot_apikey_test').prop('title', '<?php _e('Unexpected error occurred, try again', 'uptime-robot-widget'); ?>');
					$('#uptimerobot_apikey_test').css('color', '#ffb900');
				}
			});
		});
		$('#uptimerobot_get_psps').click(function() {
			$(this).addClass('loader');
			$.ajax({
				method: 'POST',
				data: 'api_key=' + $('#uptimerobot_apikey').val(),
				url: 'https://api.uptimerobot.com/v2/getPSPs',
				success: function(response) {
					var $psps_options = $("#uptimerobot_psp_url");
					$psps_options.empty();
					response.psps.forEach(function(psp) {
						if(psp.custom_url) {
							$psps_options.append(
								$("<option />").val(psp.custom_url).text(psp.custom_url)
							);
						}
						else if(psp.standard_url) {
							$psps_options.append(
								$("<option />").val(psp.standard_url).text(psp.standard_url)
							);
						}
					});
				}
			});
			$(this).removeClass('loader');
		});
	});
	</script>
<?php }

// Display settings page
function uptimerobot_settings_page() {
	// Delete cache after settings update
	if(($_GET['page']=='uptime-robot-settings') && ($_GET['settings-updated']=='true')) {
		global $wpdb;
		$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_uptimerobot_widget_cache_%'");
	} ?>
	<div class="wrap">
		<h1 class="wp-heading-inline">Uptime Robot Widget</h1>
		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="postbox-container-2" class="postbox-container">
					<div id="normal-sortables" class="meta-box-sortables">
						<form id="uptimerobot-form" method="post" action="options.php">
							<?php settings_fields('uptimerobot_settings'); ?>
							<div class="postbox">
								<div class="inside">
									<table class="form-table"><tbody>
										<tr>
											<th><?php _e('API key', 'uptime-robot-widget'); ?></th>
											<td>
												<div style="display:flex; align-items:center;"><input type="text" size="40" name="uptimerobot_apikey" id="uptimerobot_apikey" value="<?php echo get_option('uptimerobot_apikey') ?>" /><span id="uptimerobot_apikey_test" class="dashicons dashicons-editor-help uptimerobot-icon" title="<?php _e('Test the API Key', 'uptime-robot-widget'); ?>"></span></div>
												<p class="description"><?php printf(__('To get your API key visit <a target="_blank" href="%s">Uptime Robot webpage</a>.', 'uptime-robot-widget'), 'https://uptimerobot.com/dashboard#mySettings'); ?></p>
											</td>
										</tr>
										<tr>
											<th><?php _e('Uptime ratio', 'uptime-robot-widget'); ?></th>
											<td>
												<fieldset>
													<label for="uptimerobot_show_ratio"><input name="uptimerobot_show_ratio" id="uptimerobot_show_ratio" type="checkbox" value="1" <?php checked(1, get_option('uptimerobot_show_ratio', true)); ?> /><?php _e('Show uptime ratio', 'uptime-robot-widget'); ?></label></br>
													<?php _e('In the period of', 'uptime-robot-widget'); ?>&nbsp;<input name="uptimerobot_custom_period" id="uptimerobot_custom_period" type="number" min="0" value="<?php echo get_option('uptimerobot_custom_period', 14); ?>" />&nbsp;<?php _e('days', 'uptime-robot-widget'); ?>
													<p class="description"><?php _e('Leave 0 to use ratio since the monitor is created.', 'uptime-robot-widget'); ?></p>
												</fieldset>
											</td>
										</tr>
										<tr>
											<th><?php _e('Public status page', 'uptime-robot-widget'); ?></th>
											<td>
												<fieldset>
													<label for="uptimerobot_show_psp_link"><input name="uptimerobot_show_psp_link" id="uptimerobot_show_psp_link" type="checkbox" value="1" <?php checked(1, get_option('uptimerobot_show_psp_link', false)); ?> /><?php _e('Show link to the public status page', 'uptime-robot-widget'); ?></label></br>
													<div style="display:flex; align-items:center;"><select name="uptimerobot_psp_url" id="uptimerobot_psp_url"><option value="<?php echo get_option('uptimerobot_psp_url') ?>" selected /><?php echo get_option('uptimerobot_psp_url') ?></option></select><span id="uptimerobot_get_psps" class="dashicons dashicons-update uptimerobot-icon" title="<?php _e('Get the list of public status pages', 'uptime-robot-widget'); ?>"></span></div>
												</fieldset>
											</td>
										</tr>
									</tbody></table>
								</div>
							</div>
							<?php submit_button(__('Save settings', 'grecaptcha'), 'primary', 'submit', false); ?>
						</form>
					</div>
				</div>
				<div id="postbox-container-1" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables">
						<div class="postbox">
							<div class="inside">
								<p><?php _e('If you like this plugin please give a review at WordPress.org.', 'uptime-robot-widget'); ?></p>
								<p class="rating-stars"><a href="https://wordpress.org/support/plugin/uptime-robot-widget/reviews/?rate=5#new-post" target="_blank"><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span><span class="dashicons dashicons-star-filled"></span></a></p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php }
