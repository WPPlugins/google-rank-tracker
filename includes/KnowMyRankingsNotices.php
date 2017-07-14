<?php /**
 * Main Plugin Class
 *
 * @author Peter M. <topdevs.net@gmail.com>
 */

if ( ! class_exists( "KnowMyRankingsNotices" ) ) {
	
	class KnowMyRankingsNotices {
		
		function __construct() {

		}

		/**
		 * Show notice
		 * 
		 */
		
		public function show_notice( $message, $status = "updated" ) {

			if ( is_array( $message ) ) {

				$new_message = "";
				
				foreach ( $message as $key => $value ) {
					
					$new_message .= "$key {$value[0]}<br/>";	
				}

				$message = $new_message;
			}

			printf( "<div class=\"%s below-h2\"><p>%s</p></div>", $status, $message );

		}

		/**
		 * Add URL error
		 * 
		 */
		
		public function add_url_error() {

			$this->show_notice( __( "Problem adding URL report", "know-my-rankings" ), "error" );

		}

		/**
		 * Delete URL error
		 * 
		 */
		
		public function delete_url_error() {

			$this->show_notice( __( "Problem deleting URL report", "know-my-rankings" ), "error" );

		}

		/**
		 * Delete keyword error
		 * 
		 */
		
		public function delete_keyword_error() {

			$this->show_notice( __( "Problem deleting keyword from URL report", "know-my-rankings" ), "error" );

		}

		/**
		 * Installation Success
		 * 
		 */

		public function installation_success() {

			$this->show_notice( __( "The KnowMyRankings plugin installation was successful", "know-my-rankings" ) );

		}
	}
}