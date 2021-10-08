<?php

/** @package  */
class pgcalSettings {
	/**
	 * Holds the values to be used in the fields callbacks
	 */
	private $options;

	/**
	 * Start up
	 */
	public function __construct() {
		add_action('admin_menu', array($this, 'pgcal_add_plugin_page'));
		add_action('admin_init', array($this, 'pgcal_page_init'));
	}

	/**
	 * Add options page
	 */
	public function pgcal_add_plugin_page() {
		// This page will be under "Settings"
		add_options_page(
			'Settings Admin',
			'Pretty Google Calendar Settings',
			'manage_options',
			'pgcal-setting-admin',
			array($this, 'pgcal_create_admin_page')
		);
	}

	/**
	 * Options page callback
	 */
	public function pgcal_create_admin_page() {
		// Set class property
		$this->options = get_option('pgcal_settings');
?>
		<div class="pgcal-settings-header">

			<div class="pgcal-logo">
				<svg version="1.1" width="141" height="146" viewBox="0 0 141 146" xmlns="http://www.w3.org/2000/svg">
					<path d="M13.3 126.4v-89c0-2.4.9-4.5 2.6-6.3s3.8-2.6 6.2-2.6h8.8v-6.7c0-3.1 1.1-5.7 3.2-7.9 2.2-2.2 4.7-3.3 7.8-3.3h4.4c3 0 5.6 1.1 7.8 3.3s3.2 4.8 3.2 7.9v6.7h26.4v-6.7c0-3.1 1.1-5.7 3.2-7.9 2.2-2.2 4.7-3.3 7.8-3.3h4.4c3 0 5.6 1.1 7.8 3.3s3.2 4.8 3.2 7.9v6.7h8.8c2.4 0 4.4.9 6.2 2.6 1.7 1.8 2.6 3.8 2.6 6.3v88.9c0 2.4-.9 4.5-2.6 6.3s-3.8 2.6-6.2 2.6H22.1c-2.4 0-4.4-.9-6.2-2.6-1.7-1.8-2.6-3.8-2.6-6.2zm8.8 0h96.8V55.2H22.1v71.2zm17.6-84.5c0 .6.2 1.2.6 1.6s.9.6 1.6.6h4.4c.6 0 1.2-.2 1.6-.6s.6-.9.6-1.6v-20c0-.6-.2-1.2-.6-1.6s-.9-.6-1.6-.6h-4.4c-.6 0-1.2.2-1.6.6s-.6 1-.6 1.6v20zm52.8 0c0 .6.2 1.2.6 1.6s.9.6 1.6.6h4.4c.6 0 1.2-.2 1.6-.6s.6-.9.6-1.6v-20c0-.6-.2-1.2-.6-1.6s-.9-.6-1.6-.6h-4.4c-.6 0-1.2.2-1.6.6s-.6 1-.6 1.6v20z" />
					<text transform="scale(.98902 1.0111)" x="60" y="69.305733" font-family="Z003" font-size="19.485px" letter-spacing="0" stroke-width="1.218" text-anchor="middle" word-spacing="0" style="line-height:1.25" xml:space="preserve">
						<tspan x="60" y="69.305733">Pretty</tspan>
						<tspan x="60" y="95.274574">Google</tspan>
						<tspan x="65" y="121.24342">Calendar</tspan>
					</text>
				</svg>
			</div>
			<h1>Pretty Google Calendar Settings</h1>
			<p>
				<button><a href="https://github.com/sponsors/lbell">Sponsor</a></button>
			</p>
		</div>
		<form method="post" action="options.php">
			<?php
			// This prints out all hidden setting fields
			settings_fields('pgcal_option_group');
			do_settings_sections('pgcal-setting-admin');
			submit_button();
			?>
		</form>
		</div>
<?php
	}

	/**
	 * Register and add settings
	 */
	public function pgcal_page_init() {
		register_setting(
			'pgcal_option_group', // Option group
			'pgcal_settings', // Option name
			array($this, 'pgcal_sanitize') // Sanitize
		);

		add_settings_section(
			'pgcal-main-settings',
			'Usage',
			array($this, 'pgcal_pring_main_info'), // Callback
			'pgcal-setting-admin' // Page
		);

		add_settings_field(
			'google_api',
			'Google API',
			array($this, 'pgcal_gapi_callback'), // Callback
			'pgcal-setting-admin', // Page
			'pgcal-main-settings' // Section
		);

		add_settings_field(
			'use_tooltip',
			'Use Tooltip',
			array($this, 'pgcal_tooltip_callback'),
			'pgcal-setting-admin',
			'pgcal-main-settings'
		);
	}

	/**
	 * Sanitize each setting field as needed
	 *
	 * @param array $input Contains all settings fields as array keys
	 */
	public function pgcal_sanitize($input) {
		$sanitized_input = array();
		if (isset($input['google_api']))
			// TODO test api?
			$sanitized_input['google_api'] = $input['google_api'];

		if (isset($input['use_tooltip']))
			$sanitized_input['use_tooltip'] = sanitize_text_field($input['use_tooltip']);

		return $sanitized_input;
	}

	/**
	 * Print the Section text
	 */
	public function pgcal_pring_main_info() {
		print '
			<p>Shortcode Usage: [pretty_google_calendar gcal="address@group.calendar.google.com"] </p>
      		<p>You must have a google calendar API. See: <a href="https://fullcalendar.io/docs/google-calendar">https://fullcalendar.io/docs/google-calendar</a></p>
	  ';
	}


	/**
	 * Get the settings option array and print one of its values
	 */
	public function pgcal_gapi_callback() {
		printf(
			'<input type="text" id="google_api" name="pgcal_settings[google_api]" value="%s" />',
			isset($this->options['google_api']) ? esc_attr($this->options['google_api']) : ''
		);
	}


	public function pgcal_tooltip_callback() {
		printf(
			'<input title="Use the popper/tooltip plugin to display event information." type="checkbox" id="use_tooltip" name="pgcal_settings[use_tooltip]" value="yes" %s />',
			isset($this->options['use_tooltip']) ? 'checked' : ''
		);
	}
}
