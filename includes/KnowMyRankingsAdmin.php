<?php
/**
 * Main Plugin Class
 *
 * @author Peter M. <topdevs.net@gmail.com>
 */

if ( ! class_exists( "KnowMyRankingsAdmin" ) ) {
	
	class KnowMyRankingsAdmin {

		public $options_name	= "know_my_rankings_options";
		public $page_slug 		= "know_my_rankings";
		public $user;

		private $kmr;
		private $burst_kmr;
		private $burst_api_key = "B-Ct0OplaKRv-s3dbAs6Ig";
		
		function __construct() {

			// Get current user options
			$this->user = $this->get_user_options();

			// Check for API key and create KMR instance
			if ( $this->user['api_key'] )
				$this->kmr = new KnowMyRankings( $this->user['api_key'] );
			else
				$this->kmr = new KnowMyRankings('');

			// Create burst instance
			$this->burst_kmr = new KnowMyRankings( $this->burst_api_key );

			// Create notices instance
			$this->notices = new KnowMyRankingsNotices();

			// Add actions
			$this->add_actions();

			// Activation hook
			register_activation_hook( 'knowmyrankings-wp-plugin/know-my-rankings.php', array( $this, 'kmr_plugin_activate' ) );
		}
		
		
		/**
		 * Add WordPress action hooks
		 * 
		 */
		
		function add_actions() {
			
			// Register plugin menu page
			add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
			// Run on admin init
			add_action( 'admin_init', array( $this, 'admin_init' ) );
			
			// Register dashboard widget with the 'wp_dashboard_setup' action
			add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );

			// AJAX
			add_action( 'wp_ajax_kmr_callback', array( $this, 'kmr_ajax_callback' ) );
		}


		/**
		 * Plugin init
		 * 
		 */
		
		function admin_init() {

			wp_enqueue_style( 'know-my-rankings', plugins_url( 'css/know-my-rankings.css', __FILE__ ) );
			wp_enqueue_script( 'know-my-rankings', plugins_url( 'js/know-my-rankings.js', __FILE__ ), array('jquery') );
			
			// jQuery confirm
			wp_enqueue_script( 'jquery-confirm', plugins_url( 'js/jquery.confirm.min.js', __FILE__ ), array('jquery') );
			
			// Bootstrap for modal
			wp_enqueue_style( 'kmr-bootstrap', plugins_url( 'bootstrap/css/bootstrap.min.css', __FILE__ ) );
			wp_enqueue_script( 'kmr-bootstrap', plugins_url( 'bootstrap/js/bootstrap.min.js', __FILE__ ), array('jquery') );

			// Check if "Add new report" form submitted
			$this->add_new_action();

			// Check if user want to delte URL or keyword
			$this->delete_action();

			// Redirect if plugin activeted
			if ( get_option( 'kmr_plugin_do_activation_redirect', false ) ) {
				
				delete_option('kmr_plugin_do_activation_redirect');
				
				if ( ! isset( $_GET['activate-multi'] ) ) {
					// Redirect to Settings page
					wp_redirect( $this->get_url('settings') );
					exit();
				}
			}
		}


		/**
		 * Activation hook
		 * 
		 */

		function kmr_plugin_activate() {    
			// Option to redirect on admin_init
			add_option('kmr_plugin_do_activation_redirect', true);
		}

		
		/**
		 * Add all plugin's pages
		 * 
		 */
		
		public function add_plugin_page() {
			
			// Main menu page
			add_menu_page(
				'KnowMyRankings Settings', // page Title
				'KnowMyRankings', // menu item name
				'manage_options',
				$this->page_slug, 
				array( $this, 'reports_page' ),
				plugins_url( 'img/icon.png', __FILE__ )
			);

			// Reports Page
			add_submenu_page( 
				$this->page_slug, 
				'View Reports', 
				'View Reports', 
				'manage_options', 
				$this->page_slug
			);

			// Add new Page
			add_submenu_page( 
				$this->page_slug, 
				'New Report', 
				'New Report', 
				'manage_options', 
				$this->page_slug . '_add_new', 
				array( $this, 'add_new_page' )
			);

			// Settings Page
			add_submenu_page( 
				$this->page_slug, 
				'Settings',
				'Settings',
				'manage_options',
				$this->page_slug . '_settings',
				array( $this, 'settings_page' )
			);
		}

		
		/**
		 * Settings page callback
		 *
		 */
		
		public function settings_page() {

			// DEBUG
			//$this->set_user_options( array() );
			//var_dump($this->user);

			/**
			 * If user settings form submit
			 * 
			 */

			if ( isset( $_POST['update_user'] ) || wp_verify_nonce( $_POST['update_user'], 'settings' ) ) {

				// $_POST validation here
				$user = array (
					'email'		=> $_POST['email'],
					'password' 	=> $_POST['password']
				);

				// Get API key
				$response = $this->kmr->retrieve_api_key( $user );

				if ( $response['status'] == "OK" ) {

					// Update KMR with recent api_key
					$this->kmr = new KnowMyRankings( $response['body']['api_key'] );

					// Get current reports for widget dropdown
					$urls = $this->kmr->get_urls();

					if ( $urls['status'] == "OK" ) {

						$first_url 			= $urls['body']['urls'][0];
						$default_report_id 	= $first_url['id'];
					}
				
					$user = array (
						'api_key' 			=> $response['body']['api_key'],
						'default_report_id' => $default_report_id
					);
						
					$this->set_user_options( $user );
					$this->user = $user;
				}				

				require_once('views/settings.view.php');

				return;
			}

			/**
			 * If widget id form submit
			 * 
			 */

			if ( isset( $_POST['update_widget'] ) || wp_verify_nonce( $_POST['update_widget'], 'settings' ) ) {

				// $_POST validation here
				$default_report_id = $_POST['default_report'];

				// Update user
				$user = $this->user;
				$user['default_report_id'] = $default_report_id;

				$this->set_user_options( $user );
				$this->user = $user;

				require_once('views/settings.view.php');

				return;
			}
			
			/**
			 * If user submit Step 1
			 * 
			 */
			
			if ( isset( $_POST['step_one_nonce'] ) || wp_verify_nonce( $_POST['step_one_nonce'], 'setup' ) ) {
				
				
				$this->burst_kmr = new KnowMyRankings( $this->burst_api_key );

				// Simple POST validation
				if ( ! isset( $_POST['url'] ) || empty( $_POST['url'] ) )
					return;

				if ( ! isset( $_POST['keywords'] ) || empty( $_POST['keywords'] ) )
					return;
				
				$url 		= $_POST['url'];
				$keywords 	= $_POST['keywords'];
				$keywords 	= str_replace(",", "", $keywords);
				$keywords 	= str_replace("\r\n", ",", $keywords);

				$args = array (
					'url'		=> $url,
					'keywords'	=> $keywords
				);
				
				// Add URL to burst account to show user
				$response = $this->burst_kmr->add_url( $args );

				// Try to delete this URL
				if ( $response['status'] == "OK" ) {
					$id = $response['body']['id'];
					$this->burst_kmr->delete_url( $id );
				}

				require_once('views/setup.two.view.php');

				return;
			}
			
			
			/**
			 * If user submit Step 2
			 * 
			 */
			
			if ( isset( $_POST['step_two_nonce'] ) || wp_verify_nonce( $_POST['step_two_nonce'], 'setup' ) ) {

				// $_POST validation here
				$user = array (
					'email'		=> $_POST['email'],
					'password' 	=> $_POST['password']
				);

				// If user already have an accaunt
				if ( isset( $_POST['have_account'] ) && $_POST['have_account'] == 'true' ) {

					// Get API key
					$response = $this->kmr->retrieve_api_key( $user );
				}
				// If user create new accaunt
				else {
					// POST Validation
					if ( $_POST['password'] != $_POST['password_confirm'] ) {
					
						$response = array( 
							'status' 	=> "ERR",
							'body'  	=> "Passwords mismatch"
						);
						// Show view
						require_once('views/setup.two.view.php');
						return;
					}

					// Try to create user
					$response = $this->kmr->create_user( $user );
				}

				// Check response
				if ( $response['status'] == "OK" ) {
						
					// Update KMR with recent api_key
					$this->kmr = new KnowMyRankings( $response['body']['api_key'] );
					
					// Get report info from previous step
					$url 		= $_POST['url'];
					$keywords 	= $_POST['keywords'];

					$args = array (
						'url'		=> $url,
						'keywords'	=> $keywords
					);

					// Add report from Step 1 to current user
					$response_new_url = $this->kmr->add_url( $args );
					
					// Show notice if URL not added
					if ( $response_new_url['status'] == "ERR" )
						add_action( 'kmr_settings_notice', array( $this->notices, 'add_url_error' ) );
					else
						$default_report_id = $response_new_url['body']['id'];
					
					// Update user info in DB and instance
					$user = array (
						'api_key' 			=> $response['body']['api_key'],
						'default_report_id' => $default_report_id
					);

					$this->set_user_options( $user );
					$this->user = $user;

					add_action( 'kmr_settings_notice', array( $this->notices, 'installation_success' ) );
					
					// Show settings view
					require_once('views/setup.final.view.php');
				} 
				else {
					// Show same view
					require_once('views/setup.two.view.php');
				}

				return;
			}

			/**
			 * If we have no user saved show Setup step 1
			 * 
			 */
			
			if ( ! isset ( $this->user["api_key"] ) ) {
				
				require_once('views/setup.one.view.php');	
				return;
			}
			
			/**
			 * If we have user saved show settings screen
			 * 
			 */
			
			else {

				require_once('views/settings.view.php');
			}
		}


		/**
		 * Reports page callback
		 *
		 */
		
		public function reports_page() {

			/**
			 * If we have no user saved show Setup step 1
			 * 
			 */
			
			if ( ! isset ( $this->user["api_key"] ) ) {
				
				require_once('views/setup.one.view.php');
				return;
			}

			
			/**
			 * Show certain report
			 * 
			 */
			
			if ( isset( $_GET['report'] ) ) {

				// Send request
				$response = $this->kmr->get_url( intval ( $_GET['report'] ) );

				if ( $response["status"] == "OK" ) {

					// Check for sorting
					if ( isset( $_GET['orderby'] ) ) {
						
						$keywords 	= $response['body']['keywords'];
						$orderby 	= $_GET['orderby'];
						$sortable 	= array();

						if ( $_GET['order'] == "desc" )
							$order = SORT_DESC;
						else 
							$order = SORT_ASC;

						// Get keys
						foreach ( $keywords as $key => $keyword ) {
							$sortable[$key] = $keyword[$orderby];
						}

						// Sort
						if ( $orderby == "kw" ) {

							array_multisort( $sortable, $order, SORT_STRING, $keywords );
						}
						else {
							array_multisort( $sortable, $order, $keywords );

							// Move 0 values to the end if ASC or to front if DESC
							foreach ( $keywords as $key => $keyword ) {
								if ( $keyword['current_rank'] == 0 ) {
									
									$zero_element = $keywords[$key];
									unset( $keywords[$key] );
									
									if ( $_GET['order'] == "asc" )
										$keywords[] = $zero_element;
									else
										array_unshift( $keywords, $zero_element );
								};
							}
						}
						
						$response['body']['keywords'] = $keywords;
					}
				}

				//var_dump($response);

				require_once('views/edit.report.view.php');
			}

			/**
			 * Show all reports list
			 * 
			 */
			
			else {
				// Send request
				$response = $this->kmr->get_urls();

				if ( $response['status'] == "OK" ) {
					// get urls
					$urls 		= $response['body']['urls'];
					
					// paginate
					$per_page 		= 10;
					$total_urls 	= sizeof( $urls );
					$total_pages 	= ceil ( $total_urls / $per_page );

					$paged 			= ( isset( $_GET['paged'] ) ) 	? $_GET['paged'] : 1;
					$paged 			= ( $total_pages >= $paged ) 	? $paged : 1;
					$paged 			= ( $paged > 1 ) 				? $paged : 1;

					$offset 		= $per_page * ( $paged - 1 );
					
					// slice only current page
					$urls 		= array_slice( $urls, $offset, $per_page );
				}
				
				require_once('views/all.reports.view.php');
			}
		}


		/**
		 * Send a request to add new URL or new keywrods when form submitted
		 *
		 */

		public function add_new_action() {

			if ( isset( $_POST['add_new_report'] ) && wp_verify_nonce( $_POST['add_new_report'], 'reports' ) ) {

				// Simple POST validation
				if ( ! isset( $_POST['url'] ) || empty( $_POST['url'] ) )
					return;

				if ( ! isset( $_POST['keywords'] ) || empty( $_POST['keywords'] ) )
					return;
				
				$url 		= $_POST['url'];
				$keywords 	= $_POST['keywords'];
				$keywords 	= str_replace(",", "", $keywords);
				$keywords 	= str_replace("\r\n", ",", $keywords);

				$args = array (
					'url'		=> $url,
					'keywords'	=> $keywords
				);
				
				$response = $this->kmr->add_url( $args );

				if ( $response['status'] != "ERR" ) {
					
					$id = $response['body']['id'];

					wp_redirect( $this->get_url( "view_report", array ( "report" => $id ) ) );
					exit;
				}
				else 
					add_action( 'kmr_add_new_view_notice', array( $this->notices, 'add_url_error' ) );
			}
		}


		/**
		 * Send a request to delete URL or keywrod
		 *
		 */

		public function delete_action() {

			if ( isset( $_GET['delete_url'] ) ) {

				$id = $_GET['delete_url'];
				
				$response = $this->kmr->delete_url( $id );

				if ( $response['status'] == "ERR" ) {

					add_action( 'kmr_all_reports_notice', array( $this->notices, 'delete_url_error' ) );
				}
			}

			if ( isset( $_GET['delete_keyword'] ) ) {

				$id = $_GET['delete_keyword'];
				
				$response = $this->kmr->delete_keyword( $id );

				if ( $response['status'] == "ERR" ) {

					add_action( 'kmr_edit_report_notice', array( $this->notices, 'delete_keyword_error' ) );
				}
			}
		}


		/**
		 * Add new page callback
		 *
		 */

		public function add_new_page() {

			/**
			 * If we have no user saved show Setup step 1
			 * 
			 */
			
			if ( ! isset ( $this->user["api_key"] ) ) {
				require_once('views/setup.one.view.php');
				return;
			}

			// Simple POST validation
			$validation = "";

			if ( isset( $_POST['add_new_report'] ) && wp_verify_nonce( $_POST['add_new_report'], 'reports' ) ) {

				if ( ! isset( $_POST['url'] ) || empty( $_POST['url'] ) )
					$validation .= __( "URL cannot be empty.</br>", "know-my-rankings" );

				if ( ! isset( $_POST['keywords'] ) || empty( $_POST['keywords'] ) )
					$validation .= __( "Keywords cannot be empty.", "know-my-rankings" );
			}

			// Show Add new screen
			require_once('views/add.new.view.php');

		}


		/**
		 * Show rank report table based response
		 *
		 */

		public function show_report( $response, $action = "" ) {

			require_once('views/report.view.php');

		}

		/**
		 * Show rank report table based response
		 *
		 */

		public function show_keyword( $keyword, $url_id ) {

			require('views/keyword.view.php');

		}


		/**
		 * Add WP dashboard widget
		 *
		 */
		
		public function add_dashboard_widget() {

			// Get default URL for title
			$default_report_id = $this->user['default_report_id'];
			
			if ( $default_report_id ) {
				$response = $this->kmr->get_url( $default_report_id );

				if ( $response['status'] == "OK" )
					$report_url = " &#8212; " . $response['body']['url'];
			} 

			wp_add_dashboard_widget( 
				'know-my-rankings-widget', 
				__( 'KnowMyRankings.com', "know-my-rankings" ) . $report_url, 
				array( $this, 'show_dashboard_widget' ) 
			);

		}


		/**
		 * Show WP dashboard widget
		 *
		 */
		
		public function show_dashboard_widget() {

			$default_report_id = $this->user['default_report_id'];

			if ( $default_report_id ) {
				
				$response = $this->kmr->get_url( $default_report_id );

				if ( $response['status'] == "OK" ) {

					$url_id = $response['body']['id'];
					
					$this->show_report( $response );
				}

				require_once('views/dashboard.widget.view.php');
			} 
			else {

				echo "<p style=\"padding: 0 1em 1em; \"><a href=\"" . $this->get_url( "settings" ) . "\">" . __( "Setup Widget on Settings Page", "know-my-rankings" ) . "</a></p>";
			}

		}


		/**
		 * Get user options
		 * 
		 */
		
		public function get_user_options() {

			$options = get_option( $this->options_name );

			return $options;
		}


		/**
		 * Set user options
		 * 
		 */
		
		public function set_user_options( $options ) {

			update_option( $this->options_name, $options );
		}


		/**
		 * Get dashboard page URL
		 */
		
		public function get_url( $page, $args = null ) {

			switch ( $page ) {

				case "add_new" :
					$url = admin_url( "admin.php?page=" . $this->page_slug . "_add_new" );
					break;

				case "settings" :
					$url = admin_url( "admin.php?page=" . $this->page_slug . "_settings" );
					break;

				case "view_report" :
					$url = admin_url( "admin.php?page=" . $this->page_slug );
					break;

				case "all_reports" :
					$url = admin_url( "admin.php?page=" . $this->page_slug );
					break;

			}

			// Add URL parameters if set
			if ( is_array( $args ) ) {
				foreach ( $args as $key => $value ) {
					$url .= "&$key=$value";
				}
			}

			return $url;
		}


		/**
		 * Get specific KMR page URL
		 * 
		 */
		
		public function get_kmr_url( $page, $param = "" ) {

			$base_url = "http://knowmyrankings.com/";

			switch ( $page ) {

				case "keywords" :
					$url = $base_url . "keywords/" . intval( $param );
					break;

				case "view_url" :
					$url = $base_url . "urls/" . intval( $param );
					break;

				case "dashboard" :
					$url = $base_url . "dashboard";
					break;

				case "upgrade" :
					$url = $base_url . "upgrade";
					break;

				default :
					$url = $base_url;
			}

			// Add api_key to immediately login user
			$url .= "?api_key=" . $this->user['api_key'];
			
			return $url;
		}


		/**
		 * Get rank classes: color, fill etc
		 * 
		 */
		
		public function get_rank_classes( $rank ) {

			$classes = array();

			if ( $rank == 1 ) {
				$classes[] = "green";
				$classes[] = "fill";
			}
			if ( $rank > 1 && $rank <= 10 ) {
				$classes[] = "green";
			}
			if ( $rank > 10 && $rank <= 20 ) {
				$classes[] = "yellow";
			}
			if ( $rank > 20 && $rank <= 100 ) {
				$classes[] = "red";
			}
			
			if ( ! $rank  ) {
				$classes[] = "gray";
				$classes[] = "fill";
			}

			return implode( " ", $classes );
			
		}

		/**
		 * Get rank value: number, 100+ or 'no data'
		 * 
		 */
		
		public function get_rank_value( $rank ) {

			if ( $rank === 0 )
				echo '100+';
			
			if ( is_null( $rank ) )
				echo "<span class=\"no-data\">" . __( "no&nbsp;data", "know-my-rankings" ) . "</span>";

			if ( is_numeric( $rank ) && $rank > 0 )
				echo $rank;

		}

		/**
		 * Echo rank classes: color, fill etc
		 * 
		 */
		
		public function position( $keyword ) {

			if ( isset ( $keyword['last_7day'] ) && $keyword['last_7day'] != 0 ) {

				$position = $keyword['last_7day'] - $keyword['current_rank'];

				if ( $position > 0 ) {
					$class = "up";
					$label = "Up $position";
				}
				if ( $position < 0 ) {
					$class = "down";
					$label = "Down " . abs( $position );
				}
				if ( $position == 0 ) {
					$label = "No change";
				}

				echo "<div class=\"position $class\">
					<span class=\"triangle\"></span>
					<span class=\"desc\">$label</span> "
					.__( "since last week", "know-my-rankings" ).
				"</div>";
			}
			else
				echo "<div class=\"position\">
					<span class=\"desc\">$label</span>&nbsp;</div>";
		}

		/**
		 * Output pagination
		 * 
		 */
		
		public function pagination( $total_urls, $per_page, $paged ) { 

			$total_pages 	= ceil ( $total_urls / $per_page );
			
			// Check if last page
			if ( $paged == $total_pages ) {

				$next_disabled 	= "disabled";
				$next_page 			= $paged;
			}
			else
				$next_page = $paged + 1;

			// Check if first page
			if ( $paged == 1 ) {

				$previous_disabled 	= "disabled";
				$previous_page 		= $paged;
			}
			else
				$previous_page = $paged - 1;

			?>
			<form method="GET" action="<?php echo $this->get_url( "all_reports" ); ?>">
				<input type="hidden" name="page" value="know_my_rankings">
				<div class="tablenav-pages">
					<span class="displaying-num"><?php printf( __( '%d items', 'know-my-rankings' ), $total_urls ); ?></span>
					<span class="pagination-links">
						<a class="first-page <?php echo $previous_disabled; ?>" 
							title="<?php _e('Go to the first page', 'know-my-rankings'); ?>" 
							href="<?php echo $this->get_url( "all_reports" ); ?>">«</a>
						<a class="prev-page <?php echo $previous_disabled; ?>" 
							title="<?php _e('Go to the previous page', 'know-my-rankings'); ?>" 
							href="<?php echo $this->get_url( "all_reports", array( 'paged' => $previous_page ) ); ?>">‹</a>
						<span class="paging-input">
							<input class="current-page" title="<?php _e( 'Current page', 'know-my-rankings' ); ?>" type="text" name="paged" value="<?php echo $paged; ?>" size="1"> 
							<?php _e( 'of', 'know-my-rankings' ); ?> <span class="total-pages"><?php echo $total_pages; ?></span>
						</span>
						<a class="next-page <?php echo $next_disabled; ?>" 
							title="<?php _e('Go to the next page', 'know-my-rankings'); ?>" 
							href="<?php echo $this->get_url( "all_reports", array( 'paged' => $next_page ) );?>">›</a>
						<a class="last-page <?php echo $next_disabled; ?>" 
							title="<?php _e('Go to the last page', 'know-my-rankings'); ?>" 
							href="<?php echo $this->get_url( "all_reports", array( 'paged' => $total_pages ) );?>">»</a>
					</span>
				</div>
			</form>
		<?php }

		/**
		 * AJAX handler
		 * 
		 */

		function kmr_ajax_callback() {

			global $wpdb; // this is how you get access to the database

			/**
			 * Price Check
			 */
			
			if ( $_POST['kmr_action'] == 'check_price' ) {

				$args = array (
					'url'		=> $_POST['url'],
					'keywords'	=> $_POST['keywords']
				);
				
				$response = $this->kmr->check_price( $args );

				if ( $response['status'] == 'OK' ) {
					
					$upgrade_url 	= "";
					$html 			= "";
					$plan 			= $response['body'];

					// Check if upgrade required
					if ( $plan['upgrade_required'] ) {
						
						$html = __("<h2>You need to upgrade to track more than 10 keywords!</h2>
							Your free KnowMyRankings.com account allows you to track up to 10 keywords. Upgrading your account to a monthly subscription will let you add <b>unlimited</b> keywords and reports.", "know-my-rankings");
						$upgrade_url = $this->get_kmr_url('upgrade');
					}
					else {
						$keywords 			= $plan['new_keywords'];
						$keywords_to_add 	= sizeof( $keywords );

						$current_price 		= $plan['current_price'] / 100;
						$new_price 			= $plan['new_price'] / 100;
						$price_increase		= $new_price - $current_price;

						$current_total_keywords 	= $plan['current_total_keywords'];
						$new_total_keywords 		= $plan['new_total_keywords'];

						ob_start();
						require_once('views/keywords.confirm.view.php');	
						$html = ob_get_clean();
					}
					
					//$html = print_r($plan, true);
					
					$json = array(
						'url' 			=> $args['url'],
						'keywords' 		=> $args['keywords'],
						'message' 		=> $html,
						'upgrade_url'	=> $upgrade_url
					);
				}

				echo json_encode( $json );
			}

			/**
			 * Add keywords
			 */
			
			if ( $_POST['kmr_action'] == 'add_keywords' ) {

				$args = array (
					'url'		=> $_POST['url'],
					'keywords'	=> $_POST['keywords']
				);
				
				$response = $this->kmr->add_url( $args );

				if ( $response['status'] == 'OK' ) {

					$this->show_report( $response );
				}
				else {
					echo $response['body'];
				}
			}

			/**
			 * Get keywords
			 */
			
			if ( $_POST['kmr_action'] == 'get_keyword' ) {

				$keyword_id 	= $_POST['keyword_id'];
				$url_id 		= $_POST['url_id'];
				$update_action	= $_POST['update_action'];

				if ( $update_action == 'burst' ) {
					$response = $this->burst_kmr->get_keyword( $keyword_id );
				}
				else
					$response = $this->kmr->get_keyword( $keyword_id );

				if ( $response['status'] == 'OK' ) {

					$is_loaded = false;
					$i = 1; // requests iterator

					while ( ! $is_loaded && $i < 20 ) {
						
						$is_loaded = true;
						$i++;

						sleep(2);
						
						// send request
						if ( $update_action == 'burst' ) {
							$response = $this->burst_kmr->get_keyword( $keyword_id );
						}
						else
							$response = $this->kmr->get_keyword( $keyword_id );
						
						$keyword = $response['body'];
						
						if ( $keyword['current_rank'] === null )
							$is_loaded = false;
					}

					$this->show_keyword( $keyword, $url_id );

				}
				else {
					echo "<td colspan=\"6\"><b>";
					echo $response['body'];
					echo "</b></td>";
				}
			}


			die(); // this is required to return a proper result
		}


	}

	// Create plugin instance
	$kmr_admin = new KnowMyRankingsAdmin();
}

?>