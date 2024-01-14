<?php

/**
 * Admin class
 */
class MAC_Admin
{
	/**
	 * Initializes the plugin by setting filters and administration functions.
	 */
    public function __construct()
    {
        add_action( 'admin_init', array( $this, 'mac_settings_init' ) );
        add_action('admin_menu', array($this, 'mac_register_menu'));
        add_action( 'admin_enqueue_scripts', array( $this, 'mac_enqueue' ) );
        add_filter( 'upload_mimes', array($this, 'mac_add_kml_type_file') );
    }

    /**
	 * Enqueue scripts & styles
	 */
	public function mac_enqueue() {
		$screen = get_current_screen();
		if ( 'settings_page_map-area-checker' !== $screen->id ) {
			return;
		}

		wp_enqueue_media();
		wp_enqueue_script( 'mac-upload', MAC_URL . 'assets/js/upload.js', array( 'jquery' ), 1.0, true );
	}

    /**
     * Register menu
     */
    public function mac_register_menu()
    {
        add_submenu_page('options-general.php', 'Map Area Checker ', 'Map Area Checker', 'edit_posts', 'map-area-checker', array($this, 'setting_menu_callback'));
    }

    /**
     * Setting Menu Callback
     */
    public function mac_settings_init()
    {
        register_setting('mac', 'mac_apikey');
        register_setting('mac', 'mac_zoneurl');
        register_setting('mac', 'mac_inside_url');
        register_setting('mac', 'mac_outside_url');

        add_settings_section(
            'mac_section_1',
            '',
            false,
            'mac_page'
        );

        add_settings_field(
            'apikey',
            __('Mapbox Token key', 'mac'),
            array($this, 'field_apikey'),
            'mac_page',
            'mac_section_1'
        );

        add_settings_field(
            'zoneurl',
            __('Map Zone URL Json', 'mac'),
            array($this, 'field_zoneurl'),
            'mac_page',
            'mac_section_1'
        );

        add_settings_field(
            'inside_url',
            __('Inside Action URL', 'mac'),
            array($this, 'field_insideurl'),
            'mac_page',
            'mac_section_1'
        );

        add_settings_field(
            'outside_url',
            __('Outside Action URL', 'mac'),
            array($this, 'field_outside_url'),
            'mac_page',
            'mac_section_1'
        );

        add_settings_field(
            'shortcode',
            __('Shortcode', 'mac'),
            array($this, 'field_shortcode'),
            'mac_page',
            'mac_section_1'
        );

    }

    /**
     * Display field
     *
     * @param array $args Args.
     */
    public function field_apikey($args)
    {
        $val = get_option('mac_apikey');
        ?>
		<input type="password" class="regular-text" name="mac_apikey" value="<?php echo esc_attr($val); ?>">
		<?php
	}

	 /**
     * Display field
     *
     * @param array $args Args.
     */
    public function field_zoneurl($args)
    {
        $val = get_option('mac_zoneurl');
        ?>
		<input type="text" class="regular-text" name="mac_zoneurl" value="<?php echo esc_attr($val); ?>">
		<input class="mac-zoneurl button" type="button" value="<?php esc_attr_e( 'Upload file', 'sgs' ); ?>" />
		<?php
	}

	 /**
     * Display field
     *
     * @param array $args Args.
     */
    public function field_insideurl($args)
    {
        $val = get_option('mac_inside_url');
        ?>
		<input type="text" class="regular-text" name="mac_inside_url" value="<?php echo esc_attr($val); ?>">
		<?php
	}

	 /**
     * Display field
     *
     * @param array $args Args.
     */
    public function field_outside_url($args)
    {
        $val = get_option('mac_outside_url');
        ?>
		<input type="text" class="regular-text" name="mac_outside_url" value="<?php echo esc_attr($val); ?>">
		<?php
	}

	 /**
     * Display field
     *
     * @param array $args Args.
     */
    public function field_shortcode($args)
    {
        
        ?>
		[<?php echo MAC_SHORTCODE; ?>]
		<?php
	}

	/**
	 * Display settings page
	 */
	public function setting_menu_callback() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<script id="search-js" defer="" src="https://api.mapbox.com/search-js/v1.0.0-beta.18/web.js"></script>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'mac' );
				do_settings_sections( 'mac_page' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Allow .kml type upload to media library
	 */
	public function mac_add_kml_type_file(){

		$mimes['json'] = 'text/xml';
		 return $mimes;
	}
}