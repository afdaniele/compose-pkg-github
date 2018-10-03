<?php
# @Author: Andrea F. Daniele <afdaniele>
# @Date:   Wednesday, July 18th 2018
# @Email:  afdaniele@ttic.edu
# @Last modified by:   afdaniele


namespace system\packages\github;

use \system\classes\Core;
use \system\classes\Utils;
use \system\classes\Configuration;

/**
*   Utility functions for talking to the GitHub API v3
*/
class GitHub{

	private static $initialized = false;
	private static $api_protocol = "https";
	private static $api_host = "api.github.com";

	// disable the constructor
	private function __construct() {}

	/** Initializes the module.
     *
     *	@retval array
	 *		a status array of the form
	 *	<pre><code class="php">[
	 *		"success" => boolean, 	// whether the function succeded
	 *		"data" => mixed 		// error message or NULL
	 *	]</code></pre>
	 *		where, the `success` field indicates whether the function succeded.
	 *		The `data` field contains errors when `success` is `FALSE`.
     */
	public static function init(){
		if( !self::$initialized ){
			self::$initialized = true;
			return ['success' => true, 'data' => null];
		}else{
			return ['success' => true, 'data' => "Module already initialized!"];
		}
	}//init

	/** Returns whether the module is initialized.
     *
     *	@retval boolean
	 *		whether the module is initialized.
     */
	public static function isInitialized(){
		return self::$initialized;
	}//isInitialized

    /** Safely terminates the module.
     *
     *	@retval array
	 *		a status array of the form
	 *	<pre><code class="php">[
	 *		"success" => boolean, 	// whether the function succeded
	 *		"data" => mixed 		// error message or NULL
	 *	]</code></pre>
	 *		where, the `success` field indicates whether the function succeded.
	 *		The `data` field contains errors when `success` is `FALSE`.
     */
	public static function close(){
		// do stuff
		return [ 'success' => true, 'data' => null ];
	}//close



	// =======================================================================================================
	// Public functions

	public static function getAccessToken(){
		// TODO: change default value to NULL and handle the case when it is not set
		return Core::getSetting('access_token', 'github', null);
	}//getAccessToken


	public static function callAPI( $method, $group, $service=null, $action=null, $data=[], $headers=[] ){
		if( !in_array($method, ['GET']) )
			return ['success'=>false, 'data'=>sprintf('Method `%s` not supported', $method)];
		// get access token
		$token = self::getAccessToken();
		// build querystring
		$querystring = '';
		if( $method == 'GET' ){
			$data['access_token'] = $token;
			$querystring = toQueryString( array_keys($data), $data, true/*questionMarkAppend*/ );
		}
		$api_resource = [ $group ];
		if( !is_null($service) ) array_push($api_resource, $service);
		if( !is_null($action) ) array_push($api_resource, $action);
		// build url
		$url = sprintf(
			'%s://%s/%s%s',
			self::$api_protocol,
			self::$api_host,
			implode('/', $api_resource),
			$querystring
		);
		// configure a CURL object
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		if( $method == 'POST' )
			curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($post_data));
		// if( !is_null($token) && $method == 'POST' )
		array_push($headers, 'User-Agent: \\compose\\');
		array_push($headers, sprintf('Authorization: token %s', $token));
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		// call CURL
		$curl_response = curl_exec($curl);
		$curl_res = curl_getinfo($curl);
		curl_close($curl);
		// handle errors
		if( $curl_response === false || $curl_res['http_code'] != 200 ){
			return [
				'success'=>false,
				'data'=>sprintf(
					'An error occurred while talking to the GitHub API. The server returned the code <strong>%d</strong>.',
					$curl_res['http_code']
				)
			];
		}
		// success, decode and return answer
		$decoded = json_decode($curl_response, true);
		return [ 'success'=>true, 'data'=>$decoded ];
	}//callAPI



	// =======================================================================================================
	// Private functions

	// YOUR PRIVATE METHODS HERE

}//GitHub
?>
