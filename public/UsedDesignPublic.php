<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.used-design.com
 * @since      0.0.1
 *
 * @package    UsedDesign
 * @subpackage UsedDesign/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    UsedDesign
 * @subpackage UsedDesign/public
 * @author     used-design <info@used-design.com>
 */
class UsedDesignPublic {

	/**
	 * The ID of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    0.0.1
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    0.0.1
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->register_offers_grid_view_short_code();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in UsedDesignLoader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The UsedDesignLoader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/usedDesignPublic.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in UsedDesignLoader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The UsedDesignLoader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/usedDesignPublic.js', array( 'jquery' ), $this->version, false );

	}



	/**
	 * Register short code to place offer grid view
	 *
	 * @access	public
	 * @param	 
	 * @return	
	 */
	private function register_offers_grid_view_short_code()
	{
		add_shortcode( 'used_design_offers_grid', 'used_design_offers_grid_func' );
	}


	/**
	 * Get offers from used-design server
	 *
	 * @access	public
	 * @param	 
	 * @return	array $offers
	 */
	static function getOffers($atts)
	{
		$attributes = shortcode_atts( array(
            'cat-main' => false,
			'cat-sub' => false,
            'manufacturer' => false,
            's' => false,
            'global' => false,
			'show' => false,
		), $atts );

		$apiToken = get_option('useddesign_api_token');
        $url = ($attributes['global']) ? USEDDESIGN_API_URL . '/offer/global?api_token=' . $apiToken . '&status=online' : USEDDESIGN_API_URL . '/offer?api_token=' . $apiToken . '&status=online';

        // Filter main category
        if ($attributes['cat-main'])
        {
            $url .= '&cat-main=' . $attributes['cat-main'];
        }

        // Filter sub category
        if ($attributes['cat-sub'])
        {
        	$url .= '&cat-sub=' . $attributes['cat-sub'];
        }

        // Filter manufacturer
        if ($attributes['manufacturer'])
        {
            $url .= '&manufacturer=' . $attributes['manufacturer'];
        }


        // Free type search
        if ($attributes['s'])
        {
            $url .= '&s=' . urlencode($attributes['s']);
        }


        // limit number of restuls
        if ($attributes['show'])
        {
            $url .= '&show=' . $attributes['show'];
        }

        $ch = curl_init();
        curl_setopt_array($ch, array(
        	CURLOPT_URL => $url,
        	CURLOPT_RETURNTRANSFER => 1,
        	CURLOPT_CONNECTTIMEOUT => 10,
        	CURLOPT_FAILONERROR => true,
        	CURLOPT_HTTPHEADER => array('Accept: application/json'),
        ));


        if (! $data = curl_exec($ch))
        {
        	$response['error'] = 'Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
        }
        else
        {
        	// $response['data'] = json_decode($data, true);
        	$response = json_decode($data, true);

        }

    	curl_close($ch);
        return $response;
	}



}
