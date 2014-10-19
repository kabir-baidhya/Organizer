<?php 

namespace Gckabir\Organizer;

use Gckabir\Organizer\AwesomeCache\CacheData;


class OZR {

	private static $config = array (
		'serverUrl'	=> '',
		'cacheDir'	=> '_organizer-cache/',
		'cacheExpiration'	=> 1296000, //15-days
		'automaticServe'	=> true,
		'signature'			=> true,
		'css'	=> array(
			'basePath' 			=> 'css/',
			'minify'			=> false,
			'cache'				=> false,
			'parameter'	=> '_OZRcssSX',
			), 
		'javascript'	=> array(

			'useStrict'=> false,
			'basePath' 	=> 'scripts/',
			'wrap'		=> false, 
			'dependencies' => array(),
			'minify'	=> false,
			'cache'		=> false,
			'parameter'	=> '_OZRjsSX',
			),
		);

	private static $organizedJs;
	private static $organizedCss;


	/* Static Methods */
	public static function init( array $config)
	{
		static::$config['serverUrl'] = $_SERVER['REQUEST_URI'];//default
		
		# Overriding config
		foreach($config as $key => $value) {
			if(!is_array($value)) {
				static::$config[$key] = $value;
			} else {
				
				foreach ($value as $var => $varValue) {
					static::$config[$key][$var] = $varValue;
				}

			}
		}

		# Initialize Cache

		$cacheConfig = array(
			'directory' 	=> static::$config['cacheDir'],
			'cacheExpiry'	=> static::$config['cacheExpiration'],
			'serialize'		=> false,
			);

		CacheData::config($cacheConfig);

		if(static::$config['automaticServe']) {
			static::serve();
		}
	}

	public static function getConfig($var = null) {

		if($var) {
			return static::$config[$var];
		} else {
			return static::$config;
		}
	}

	public static function organizeJS($bundle, array $scripts = array(), $version = '1.0')
	{
		return new Javascript($bundle, $scripts, $version);
	}

	public static function organizeCSS($bundle, array $styles = array(), $version = '1.0')
	{
		return new Css($bundle, $styles, $version);
	}

	public static function serve()
	{
		$jsQuery = static::$config['javascript']['parameter'];
		$cssQuery = static::$config['css']['parameter'];

		
		if(isset($_GET[$jsQuery])) {

			$bundle = $_GET[$jsQuery];
			$contentType = 'text/javascript';

		} else if(isset($_GET[$cssQuery]))  {

			$bundle = $_GET[$cssQuery];
			$contentType = 'text/css';
		} 

		if(@$bundle) {

			$content = new CacheData($bundle);
			if($content->isCachedAndUsable() )
			{
				header('Content-Type: '.$contentType);
				echo $content->cachedData();
			} else {
				# invalid query | cache expired | file not found
				header('HTTP/1.1 404 Not Found');
				echo "<h1>404 Not Found</h1>";
			}
			die();
		} 
	}
}