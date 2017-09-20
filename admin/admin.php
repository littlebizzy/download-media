<?php

/**
 * Download Media - Admin class
 *
 * @package Download Media
 * @subpackage Download Media Admin
 */
final class DWNMDA_Admin {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Single class instance
	 */
	private static $instance;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Create or retrieve instance
	 */
	public static function instance() {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self;

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct() {

		// Download control
		add_action('admin_init', array(&$this, 'admin_init'));

		// Action rows in the media list view
		add_filter('media_row_actions', array(&$this, 'media_row_actions'), 10, 2);
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check current screen
	 */
	public function admin_init() {

		// Check download params
		if (empty($_GET['dwnmda_post']) || empty($_GET['dwnmda_nonce']))
			return;

		// Process request
		$this->download_request();
	}



	/**
	 * Alter the Media Rows actions
	 */
	public function media_row_actions($actions, $post) {

		// Check array
		if (empty($actions) || !is_array($actions))
			$actions = array();

		// Check custom action
		if (!isset($actions['dwnmda_download']))
			$actions['dwnmda_download'] = '<a href="'.esc_url($this->download_url($post->ID)).'">Download</a>';

		// Done
		return $actions;
	}



	// Internal
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Composes the download URL
	 */
	private function download_url($post_id) {
		return add_query_arg(array(
			'dwnmda_post'  => $post_id,
			'dwnmda_nonce' => wp_create_nonce($post_id.DWNMDA_FILE),
		), admin_url());
	}



	/**
	 * Check a download request
	 */
	private function download_request() {

		// Cast identifier
		$post_id = (int) $_GET['dwnmda_post'];

		// Verifiy a valid nonce
		if (!wp_verify_nonce($_GET['dwnmda_nonce'], $post_id.DWNMDA_FILE))
			wp_die('Download aborted due security verification. Please go back, refresh the page and try again.');

		// Retrieve attachment path
		$path = get_attached_file($post_id);
		if (empty($path) || !@file_exists($path))
			wp_die('Unable to retrieve the attachment file');

		// Download file
		require_once DWNMDA_PATH.'/admin/download.php';
		DWNMDA_Download::instance($path);
	}



}