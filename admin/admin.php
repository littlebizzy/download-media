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
		add_action('current_screen', array(&$this, 'current_screen'));

		// Action rows in the media list view
		add_filter('media_row_actions', array(&$this, 'media_row_actions'), 10, 2);
	}



	// WP Hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check current screen
	 */
	public function current_screen() {

		// Check screen
		$screen = get_current_screen();
		if (empty($screen) || !is_object($screen) || !is_a($screen, 'WP_Screen'))
			return;

		// Check screen context
		if ('post' != $screen->base || 'attachment' != $screen->post_type)
			return;

		// Check params
		if (empty($_GET['action']) || 'dwnmda_download' != $_GET['action'] ||
			empty($_GET['post']) || empty($_GET['nonce']))
			return;

		// Check download
		$this->download_check();
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
			'post' 	 => $post_id,
			'action' => 'dwnmda_download',
			'nonce'  => wp_create_nonce($post_id.DWNMDA_FILE),
		), admin_url('post.php'));
	}



	/**
	 * Check a download request
	 */
	private function download_request() {

		// Cast identifier
		$post_id = (int) $_GET['post'];

		// Verifiy a valid nonce
		if (!wp_verify_nonce($_GET['nonce'], $post_id.DWNMDA_FILE))
			wp_die('Download aborted due security verification. Please go back, refresh the page and try again.');

		// Retrieve attachment URL
		$url = wp_get_attachment_url((int) $post_id);
		if (empty($url))
			wp_die('Unable to retrieve the attachment URL');

		// Download file
		require_once DWNMDA_PATH.'/admin/download.php';
		DWNMDA_Download::instance($url);
	}



}