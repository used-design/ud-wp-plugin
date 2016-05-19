<?php

class UsedDesignUpdater {

	private $file;

	private $plugin;

	private $basename;

	private $active;

	private $username = 'used-design';

	private $repository = 'ud-wp-plugin';

	private $authorize_token;

	private $github_response;

	
	public function __construct( $file ) {
		$this->file = dirname(dirname($file)) . '/used-design.php';
	}

	/**
	 * Set plugin properties
	 *
	 * @access	public
	 */
	public function set_plugin_properties() {
		$this->plugin	= get_plugin_data( $this->file );
		$this->basename = plugin_basename( $this->file );
		$this->active	= is_plugin_active( $this->basename );
	}

	/**
	 * Fetch plugin version information from GitHub
	 *
	 * @access	private
	 */
	private function get_repository_info() {
	    if ( is_null( $this->github_response ) ) { // Do we have a response?
	        $request_uri = sprintf( 'https://api.github.com/repos/%s/%s/releases', $this->username, $this->repository ); // Build URI

	        if( $this->authorize_token ) { // Is there an access token?
	            $request_uri = add_query_arg( 'access_token', $this->authorize_token, $request_uri ); // Append it
	        }

	        $response = json_decode( wp_remote_retrieve_body( wp_remote_get( $request_uri ) ), true ); // Get JSON and parse it

	        if( is_array( $response ) ) { // If it is an array
	            $response = current( $response ); // Get the first item
	        }

	        if( $this->authorize_token ) { // Is there an access token?
	            $response['zipball_url'] = add_query_arg( 'access_token', $this->authorize_token, $response['zipball_url'] ); // Update our zip url with token
	        }

	        $this->github_response = $response; // Set it to our property
	    }
	}


	/**
	 * Modify transient
	 *
	 * @access	public
	 * @param	$transient
	 * @return	$transient
	 */
	public function modify_transient( $transient ) {

		if( property_exists( $transient, 'checked') ) { // Check if transient has a checked property

			if( $checked = $transient->checked ) { // Did Wordpress check for updates?

				$this->get_repository_info(); // Get the repo info

				$out_of_date = version_compare( $this->github_response['tag_name'], $checked[ $this->basename ], 'gt' ); // Check if we're out of date

				if( $out_of_date ) {

					$new_files = $this->github_response['zipball_url']; // Get the ZIP

					$slug = current( explode('/', $this->basename ) ); // Create valid slug

					$plugin = array( // setup our plugin info
						'url' => $this->plugin["PluginURI"],
						'slug' => $slug,
						'package' => $new_files,
						'new_version' => $this->github_response['tag_name']
					);

					$transient->response[$this->basename] = (object) $plugin; // Return it in response
				}
			}
		}

		return $transient; // Return filtered transient
	}


	/**
	 * Modify plugin popup info screen
	 *
	 * @access	public
	 * @param	$result
	 * @param	$action
	 * @param	$args
	 * @return	$plugin || $result
	 */
	public function plugin_popup( $result, $action, $args ) {

		if( ! empty( $args->slug ) ) { // If there is a slug
			
			if( $args->slug == current( explode( '/' , $this->basename ) ) ) { // And it's our slug

				$this->get_repository_info(); // Get our repo info

				// Set it to an array
				$plugin = array(
					'name'				=> $this->plugin["Name"],
					'slug'				=> $this->basename,
					'requires'					=> '4.0',
					'tested'						=> '4.5.2',
					/*'rating'						=> '100.0',
					'num_ratings'				=> '12345',
					'downloaded'				=> '12345',*/
					'added'							=> '2016-05-18',
					'version'			=> $this->github_response['tag_name'],
					'author'			=> $this->plugin["AuthorName"],
					'author_profile'	=> $this->plugin["AuthorURI"],
					'last_updated'		=> $this->github_response['published_at'],
					'homepage'			=> $this->plugin["PluginURI"],
					'short_description' => $this->plugin["Description"],
					'sections'			=> array(
						'Description'	=> $this->plugin["Description"],
						'Updates'		=> $this->github_response['body'],
					),
					'download_link'		=> $this->github_response['zipball_url']
				);

				return (object) $plugin; // Return the data
			}

		}
		return $result; // Otherwise return default
	}


	/**
	 * Reactivate plugin after update has been installed
	 *
	 * @access	public
	 * @param	$response
	 * @param	$hook_extra
	 * @param	$result
	 * @return	$result
	 */
	public function after_install( $response, $hook_extra, $result ) {
		global $wp_filesystem; // Get global FS object

		$install_directory = plugin_dir_path( $this->file ); // Our plugin directory
		$wp_filesystem->move( $result['destination'], $install_directory ); // Move files to the plugin dir
		$result['destination'] = $install_directory; // Set the destination for the rest of the stack

		if ( $this->active ) { // If it was active
			activate_plugin( $this->basename ); // Reactivate
		}

		return $result;
	}
}
