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
		'html'	=> array(
			'parameter'	=> '_OZRhtmlSX',
			'basePath' 	=> 'templates/',
			'minify'	=> false,
			'cache'		=> false,
			)
		);

	private static $organizedJs;
	private static $organizedCss;
	private static $initialized = false;

	/* Static Methods */
	public static function init( array $config = array())
	{
		if(isset($_SERVER['REQUEST_URI'])) {
			static::$config['serverUrl'] = $_SERVER['REQUEST_URI'];//default
		}
		
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


		static::$initialized = true;
		
		if(static::$config['automaticServe']) {
			static::serve();
		}
	}

	public static function getConfig($var = null) {

		static::checkInitialized();

		if($var) {
			return static::$config[$var];
		} else {
			return static::$config;
		}
	}

	public static function organizeJS($bundle, array $scripts = array(), $version = '1.0')
	{
		static::checkInitialized();
		return new Javascript($bundle, $scripts, $version);
	}

	public static function organizeCSS($bundle, array $styles = array(), $version = '1.0')
	{
		static::checkInitialized();
		return new Css($bundle, $styles, $version);
	}

	public static function organizeHTML($bundle, array $templates = array(), $version = '1.0') 
	{
		static::checkInitialized();
		return new Html($bundle, $templates, $version);
	}

	public static function serve()
	{
		static::checkInitialized();
		$jsQuery = static::$config['javascript']['parameter'];
		$cssQuery = static::$config['css']['parameter'];
		$htmlQuery = static::$config['html']['parameter'];

		
		if(isset($_GET[$jsQuery])) {

			$bundle = $_GET[$jsQuery];
			$contentType = 'text/javascript';

		} else if(isset($_GET[$cssQuery]))  {

			$bundle = $_GET[$cssQuery];
			$contentType = 'text/css';

		} else if(isset($_GET[$htmlQuery])) {
			$bundle = $_GET[$htmlQuery];
			$contentType = 'text/html';

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

	private static function checkInitialized() {
		if(!static::$initialized){

			throw new OrganizerException("Organizer not initialized");
			die();
		}
	}
}