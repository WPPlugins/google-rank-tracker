<?php
/**
 * Main Plugin Class
 *
 * @author Peter M. <topdevs.net@gmail.com>
 */

if ( ! class_exists( "KnowMyRankings" ) ) { 
	
	
	class KnowMyRankings {

		private $api_url = "https://api.knowmyrankings.com/v1/";
		private $api_key;
		
		function __construct( $api_key ) {

			$this->api_key = $api_key;
			
		}
		
		
		/**
		 * Retrieve API key
		 * 
		 */
		
		function retrieve_api_key( $args = array() ) {

			$url = $this->api_url . "retrieve_api_key";

			$request_args = array(
				'sslverify'		=> false,
				'headers' 		=> array(
					'Authorization' => 'Basic ' . base64_encode( $args['email'] . ':' . $args['password'] )
					)
			);

			// Send HTTPS GET
			$server_response = wp_remote_get( $url, $request_args );

			return $this->check_response( $server_response );

		}

		
		/**
		 * Create user
		 * 
		 */
		
		function create_user( $args = array() ) {

			$url = $this->api_url . "users";

			$request_args = array(
				'sslverify'	=> false,
				'body'		=> $args
			);

			// Send HTTPS POST
			$server_response = wp_remote_post( $url, $request_args );

			return $this->check_response( $server_response );

		}

		
		/**
		 * Get URLs
		 * 
		 */
		
		function get_urls() {

			$url = $this->api_url . "urls";

			// Add API key
			$url .= "?api_key=" . $this->api_key; 

			// Disable SSL check
			$request_args = array (
				'sslverify'	=> false,
			);

			// Send HTTPS GET
			$server_response = wp_remote_get( $url, $request_args );

			return $this->check_response( $server_response );

		}

		/**
		 * Get URL
		 * 
		 */
		
		function get_url( $id ) {

			$url = $this->api_url . "urls/" . intval ( $id );

			// Add API key
			$url .= "?api_key=" . $this->api_key; 

			// Disable SSL check
			$request_args = array (
				'sslverify'	=> false,
			);

			// Send HTTPS GET
			$server_response = wp_remote_get( $url, $request_args );

			return $this->check_response( $server_response );

		}

		/**
		 * Get URL
		 * 
		 */
		
		function get_keyword( $id ) {

			$url = $this->api_url . "keywords/" . intval ( $id );

			// Add API key
			$url .= "?api_key=" . $this->api_key; 

			// Disable SSL check
			$request_args = array (
				'sslverify'	=> false,
			);

			// Send HTTPS GET
			$server_response = wp_remote_get( $url, $request_args );

			return $this->check_response( $server_response );

		}

		/**
		 * Add URL
		 * 
		 */
		
		function add_url( $args = array() ) {

			// Add APi key
			$args['api_key'] = $this->api_key;

			// Build query URL
			$url = $this->api_url . "urls";
			$url .= "?" . http_build_query( $args );

			$request_args = array(
				'sslverify'	=> false
			);

			// Send HTTPS POST
			$server_response = wp_remote_post( $url, $request_args );

			return $this->check_response( $server_response );
		}

		/**
		 * Add URL
		 * 
		 */
		
		function check_price( $args = array() ) {

			// Add APi key
			$args['api_key'] = $this->api_key;

			// Build query URL
			$url = $this->api_url . "urls/check_price";
			$url .= "?" . http_build_query( $args );

			$request_args = array(
				'sslverify'	=> false
			);

			// Send HTTPS POST
			$server_response = wp_remote_post( $url, $request_args );

			return $this->check_response( $server_response );
		}

		/**
		 * Delete URL
		 * 
		 */
		
		function delete_url( $id ) {

			$url = $this->api_url . "urls/" . intval ( $id );

			// Add API key
			$url .= "?api_key=" . $this->api_key; 

			// Disable SSL check
			$request_args = array (
				'method' 	=> "DELETE",
				'sslverify'	=> false,
			);

			// Send HTTPS GET
			$server_response = wp_remote_post( $url, $request_args );

			return $this->check_response( $server_response );
		}


		/**
		 * Delete keyword
		 * 
		 */
		
		function delete_keyword( $id ) {

			$url = $this->api_url . "keywords/" . intval ( $id );

			// Add API key
			$url .= "?api_key=" . $this->api_key; 

			// Disable SSL check
			$request_args = array (
				'method' 	=> "DELETE",
				'sslverify'	=> false,
			);

			// Send HTTPS GET
			$server_response = wp_remote_post( $url, $request_args );

			return $this->check_response( $server_response );
		}


		/**
		 * Check server response
		 * 
		 */

		function check_response( $server_response ) {
		
			// WP HTTP API Error
			if ( is_wp_error( $server_response ) ) {

				return array( 
					'status' 	=> "ERR",
					'body'  	=> 'WP REMOTE POST ERROR: ' . $server_response->get_error_message() 
					);
			}
			// We got response from KMR API server
			else {
				// A little bit confusing
				$status		= $server_response['response'];
				$response 	= $server_response['body'];
			}
			
			// HTTP Basic Auth failed
			if ( $status['code'] == "401" ) {
				return array( 
					'status' 	=> "ERR",
					'body'  	=> "Authorization failed"
					);
			}

			// Parse KMR server response errors like 400 or 404
			if ( $status['code'] != "200" ) {

				return array( 
					'status' 	=> "ERR",
					'body'  	=> "KnowMyRankings Server respond: (" . $status['code'] . ") " . $status['message']
					);
			}

			// JSON to array
			$response = $this->json2arr( $response );
			
			// Response JSON error
			if ( $response['status'] != "OK" ) {
				return array( 
					'status' 	=> "ERR",
					'body'  	=> $response['body']
					);
			}

			// KMR API errors
			if ( isset( $response['body']['errors'] ) ) {

				$error = array( 
					'status' 	=> "ERR",
					'body'  	=> $response['body']['errors']
					);

				return $error;
			}

			// Success
			else {
				return array( 
					'status' 	=> "OK",
					'body'  	=> $response['body']
					);
			}

		}


		/**
		 * JSON to array
		 * 
		 */
		
		function json2arr ( $json ) {

			// JSON to array
			if ( is_string( $json ) )
				$arr = json_decode( $json, true );
			else
				return array( 
					'status' 	=> "ERR",
					'body'  	=> "Server response error: response expect to be string" 
					);

			// Response JSON error
			if ( ! $arr ) {
				return array( 
					'status' 	=> "ERR",
					'body'  	=> "Server response error: wrong JSON format" 
					);
			}
			else {
				return array( 
					'status' 	=> "OK",
					'body'  	=> $arr 
					);
			}

		}

	}
}


?>