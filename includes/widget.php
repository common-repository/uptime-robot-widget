<?php
/**
 * Widget
 */

if(!defined('ABSPATH')) {
	exit;
}

// Text status of monitor
function uptimerobot_text_status($status){
	switch($status) {
		case 0:
			$r = __('paused', 'uptime-robot-widget');
			break;
		case 1:
			$r = __('n/d', 'uptime-robot-widget');
			break;
		case 2:
			$r = __('up', 'uptime-robot-widget');
			break;
		case 8:
			$r = __('seems down', 'uptime-robot-widget');
			break;
		case 9:
			$r = __('down', 'uptime-robot-widget');
			break;
		default:
			$r = __('unk', 'uptime-robot-widget');
	}
	return $r;
}

// Route status widget
function uptimerobot_widget_route_status() {
	register_rest_route('uptime-robot-widget/v1', '/status', array(
		'methods' => 'GET',
		'callback' => 'uptimerobot_widget_status',
		'permission_callback' => '__return_true'
	));
}
add_action('rest_api_init', 'uptimerobot_widget_route_status');

// Status widget
function uptimerobot_widget_status() {
	// Get fresh data
	if(false === ($data = get_transient('uptimerobot_widget_cache_'.get_locale()))) {
		// Public status page
		if(get_option('uptimerobot_show_psp_link') && !empty(get_option('uptimerobot_psp_url'))) {
			$psp = '<div class="psp"><a href="'.get_option('uptimerobot_psp_url').'" target="_blank">'.__('More details on status page', 'uptime-robot-widget').'</a></div>';
		}
		// Default error message
		$data = '<div class="error"> '.__('Oops! Something went wrong and failed to get the status, check again soon.', 'uptime-robot-widget').'</div>' . $psp;		
		// POST arguments
		$args = array(
			'body' => array(
				'api_key' => get_option('uptimerobot_apikey'),
				'format' => 'json',
				'all_time_uptime_ratio' => get_option('uptimerobot_custom_period', 0) == 0 ? '1' : '0'
			),
			'redirection' => 0,
			'httpversion' => '1.1'
		);
		if(get_option('uptimerobot_custom_period', 14)) {
			$args['body'] += ['custom_uptime_ratios' => get_option('uptimerobot_custom_period')];
		}
		// POST data
		$response = wp_remote_post('https://api.uptimerobot.com/v2/getMonitors', $args);
		// Server temporarily unavailable
		if(is_wp_error($response)) { /* error */ }
		// Verify response
		else if($response['response']['code'] == 200) {
			$json = json_decode(wp_remote_retrieve_body($response));
			// Data have monitors
			if(!empty($json->monitors)) {
				// Foreach monitors
				$monitors = '';
				foreach($json->monitors as $monitor) {
					if(get_option('uptimerobot_show_ratio')) {
						$ratio = $monitor->custom_uptime_ratio ? $monitor->custom_uptime_ratio : $monitor->all_time_uptime_ratio;
						$monitors .= '<div class="monitor"><span class="status stat'.$monitor->status.'">'.uptimerobot_text_status($monitor->status).'</span><span class="name">'.$monitor->friendly_name.'</span><span class="ratio">'.floatval(number_format($ratio, 2)).'%</span></div>';
					} else {
						$monitors .= '<div class="monitor"><span class="status stat'.$monitor->status.'">'.uptimerobot_text_status($monitor->status).'</span><span class="name">'.$monitor->friendly_name.'</span></div>';
					}
				}
				// Set data
				$data = $monitors . $psp;
				// Save data to cache
				set_transient('uptimerobot_widget_cache_'.get_locale(), $data, 60);
				// Cache header
				header('Expires: '.gmdate('d M Y H:i:s', time() + 60).' GMT');
				header('Pragma: cache');
				header('Cache-Control: max-age=60');
			}
		}
	}
	else {
		// Cache header
		header('Expires: '.gmdate('d M Y H:i:s', time() + 60).' GMT');
		header('Pragma: cache');
		header('Cache-Control: max-age=60');
	}
	// Return data
	return rest_ensure_response(array('data' => $data));
}

// Widget class
class uptimerobot_widget extends WP_Widget {
	// Widget constructor
	function __construct() {
		$widget_ops = array(
			'classname' => 'widget_uptimerobot',
			'description' => __('Status of the monitored services in the Uptime Robot service.', 'uptime-robot-widget'),
			'customize_selective_refresh' => false
		);
		parent::__construct('uptimerobot_widget', 'Uptime Robot', $widget_ops);
		// Enqueue styles & JS script if widget is active (appears in a sidebar) or if in Customizer preview
		if(is_active_widget(false, false, $this->id_base) || is_customize_preview()) {
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
		}
    }
	// Enqueue styles & JS script
	function enqueue_scripts() {
		$min = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';
		wp_enqueue_style('uptime-robot-widget', UPTIME_ROBOT_WIDGET_DIR_URL.'css/uptime-robot'.$min.'.css', array(), UPTIME_ROBOT_WIDGET_VERSION, 'all');
		wp_enqueue_script('uptime-robot-widget', UPTIME_ROBOT_WIDGET_DIR_URL.'js/js.uptimerobot'.$min.'.js', array(), UPTIME_ROBOT_WIDGET_VERSION, true);
		if(get_option('uptimerobot_show_psp_link') && !empty(get_option('uptimerobot_psp_url'))) {
			$psp_link = '<div class="psp"><a href="'.get_option('uptimerobot_psp_url').'" target="_blank">'.__('More details on status page', 'uptime-robot-widget').'</a></div>';
		}
		wp_localize_script('uptime-robot-widget', 'uptimerobot', array(
			'rest_api' => esc_url_raw(rest_url()),
			'error' => '<div class="error">'.__('Oops! Something went wrong and failed to get the status, check again soon.', 'uptime-robot-widget').'</div>'.$psp_link
		));
	}
	// Display function
	function widget($args, $instance) {
		// Widget title
		$instance['title'] = apply_filters('widget_title', $instance['title']);
		echo $args['before_widget'];
		if(!empty($instance['title'])) echo $args['before_title'].$instance['title'].$args['after_title'];
		// Widget content
		$sc = '<div id="uptimerobot" class="uptimerobot"><div class="loader" title="'.__('Loading', 'uptime-robot-widget').'..."></div></div>';
		echo $sc;
		// Widget end
		echo $args['after_widget'];
	}
	// Update function
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
        return $instance;
    }
	// Settings form function
	function form($instance) {
		$sc = '<p>
			<label for="'.$this->get_field_id('title').'">'.__('Title', 'uptime-robot-widget').':</label>
			<input class="widefat" id="'.$this->get_field_id('title').'" name="'.$this->get_field_name('title').'" type="text" value="'.$instance['title'].'" />
		</p>
		<p>
			'.sprintf(__('Enter API key in <a href="%s">plugin settings</a>.', 'uptime-robot-widget'), 'options-general.php?page=uptime-robot-settings').'
		</p>';
		echo $sc;
	}
}

// Create widget instance
add_action('widgets_init', function(){
	register_widget('uptimerobot_widget');
});
