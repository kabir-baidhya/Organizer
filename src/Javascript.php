<?php 

namespace Gckabir\Organizer  
{

	use Gckabir\Organizer\AwesomeCache\CacheData;

	class Javascript
	{
		private static $config = array (
			'useStrict'=> false,
			'basePath' 	=>  'scripts/',
			'serverUrl'	=> '',
			'cache'		=> true,
			'cacheDir'	=> '_organizer-cache/',
			'parameter'	=> '_organizer-serve',
			'minify'	=> false,
			'wrap'		=> false, 
			'dependencies'	=> array()
			);

		private $namespace = null;
		private $scripts = array();
		private $output = null;
		private $variables = array();

		public $version = '1.0';

		private function __construct($namespace, array $scripts, $version)
		{
			$this->namespace = $namespace;
			$this->scripts = $scripts;
			$this->version = $version;
		}

		/**
		 * Initialize dynamic variables (in bulk)
		 */
		public function vars(array $variables)
		{
			$this->variables = $variables;
		}

		/**
		 * Sets a value of dynamic variable
		 */
		public function setVar($key, $value) {
			$this->variables[$key]	 = $value;
		}

		/**
		 * Add new javascript file 
		 */
		public function add($js)
		{
			if(is_array($js))
			{
				$this->scripts = array_merge($this->scripts, $js);
			}
			else if(is_string($js) and !in_array($js, $this->scripts))
			{
				$this->scripts[] = $js;
			}
			return $this;
		}

		/**
		 * Add a javascript file before other scripts 
		 */
		public function addBefore($js) {
			if(is_array($js)) 
			{
				$this->scripts = array_merge($js, $this->scripts);
			}else if(is_string($js) and !in_array($js, $this->scripts)) {
				array_unshift($this->scripts, $js);
			}

			return $this;
		}

		/**
		 * Add javascript code directly 
		 */
		public function addScript($string) {
			if(!is_string($string)) {
				throw new OrganizerException("Invalid Javascript code");
			}

			$script = (object) array(
				'type' 		=> 'embeded',
				'script'	=> $string
			);
			$this->scripts[] = $script;
		}

		private function merge()
		{
			$merged = '';
			foreach($this->scripts as $singleJS)
			{
				if(is_string($singleJS)) {

					$path = static::$config['basePath'].$singleJS.'.js';
					if(file_exists($path))
					{
						ob_start();
						include_once($path);
						$script = ob_get_clean();
					} else throw new OrganizerException("{$path} not found");
					
				} else if(is_object($singleJS) and @$singleJS->type == 'embeded') {
					$script = $singleJS->script;
				} else {
					throw new OrganizerException("Invalid Javascript");
				}

				$merged .= "\n".$script;
			}

			$this->output = $merged;
		}

		private function signature() {

			return  
			'/** '."\n".
			' * '.$this->namespace.' | '.gmdate("M d Y H:i:s")." UTC\n".
			' * Organized by Organizer'."\n".
			' * https://github.com/kabir-baidhya/organizer'.
			' */'."\n";

		}
		
		public function output()
		{
			$options = static::$config;

			$javascript = $this->signature();
			
			//variables
			if($options['useStrict'] == true) {
				$javascript .= "'use strict';\n";
			}

			if($options['wrap'] == true) {

				$token= 'function';
				$lBrace = '{ ';
				$rBrace = '}';
				$lBracket = chr(40);
				$rBracket = chr(41);

				if(is_array($options['dependencies'])) {
					$depenString = trim(implode(',', $options['dependencies']) );
				} else {
					$depenString = trim($options['dependencies']);
				} 

				$javascript .= $lBracket.$token.$lBracket.$depenString.$rBracket.$lBrace."\n";
			}
			
			$javascript .= 'var $vars = '. json_encode( (object) $this->variables ).";\n";
			$javascript .= $this->output."\n";

			if($options['wrap'] == true) {
				$javascript .= $rBrace.$rBracket.$lBracket.$depenString.$rBracket.';';
			}
			return $javascript;
		}

		public function outputMinified()
		{
			return $this->signature(). \JShrink\Minifier::minify($this->output());	
		}

		public function build()
		{
			$this->merge();

			$js = new CacheData($this->namespace);
			$cacheEnabled = static::$config['cache'];

			if( !$cacheEnabled or !$js->isCachedAndUsable() )
			{
				$content = static::$config['minify'] ? $this->outputMinified() : $this->output();
				$js->putInCache($content);
			}

			$url = static::$config['serverUrl'].'?'.http_build_query(array(
				static::$config['parameter'] =>  $this->namespace,
				'ver'			=> $this->version
				));

			return $url;
		}


		/* Static Methods */
		public static function init($config)
		{
			static::$config['serverUrl'] = $_SERVER['REQUEST_URI'];//default
			static::$config = $config + static::$config;

			# Initialize Cache

			$cacheConfig = array(
				'directory' 	=>  static::$config['basePath'].static::$config['cacheDir'],
				'cacheExpiry'	=> 30 * 86400,// 30 days
				'serialize'		=> false,
				);

			CacheData::config($cacheConfig);
		}

		public static function organize($namespace, array $scripts = array(), $version = '1.0')
		{
			return new static($namespace, $scripts, $version);
		}

		public static function serve()
		{
			$parameter = static::$config['parameter'];
			if( $namespace = @$_GET[$parameter] )
			{
				$js = new CacheData($namespace);
				if($js->isCachedAndUsable() )
				{
					header('Content-Type: text/javascript');
					echo $js->cachedData();
				}
				else
				{
					header('HTTP/1.0 404 Not Found');
					echo "<h1>404 Not Found</h1>";
				}
				die();	
			}
		}
	}

}