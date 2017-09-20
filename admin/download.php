<?php

/**
 * Download Media - Download class
 *
 * @package Download Media
 * @subpackage Download Media Admin
 */
final class DWNMDA_Admin_Download {



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
	public static function instance($path = null) {

		// Check instance
		if (!isset(self::$instance))
			self::$instance = new self($path);

		// Done
		return self::$instance;
	}



	/**
	 * Constructor
	 */
	private function __construct($path = null) {

		// Check path
		if (empty($path))
			return;

		// No timeout
		@set_time_limit(0);

		// Remove existing headers
		$this->remove_headers();

		// Download file
		$this->download($path);
	}



	// Methods
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Download file
	 */
	public function download($path) {

		// Force download headers
		@header('Content-Disposition: attachment; filename="'.basename($path).'"');
		@header('Content-Type: application/octet-stream');

		// Show
		echo @file_get_contents($path);

		// End
		die;
	}



	 /**
 	 * Remove any existing header
 	 */
 	public function remove_headers() {

 		// Check headers list
 		$headers = @headers_list();
 		if (empty($headers) || !is_array($headers))
			return;

		// Check header_remove function (PHP 5 >= 5.3.0, PHP 7)
		$remove_function = function_exists('header_remove');

		// Enum and clean
		foreach ($headers as $header) {
			list($k, $v) = array_map('trim', explode(':', $header, 2));
			$remove_function? @header_remove($k) : @header($k.':');
		}
 	}



}