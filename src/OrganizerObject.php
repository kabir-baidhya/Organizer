<?php 

namespace Gckabir\Organizer;
use Gckabir\Organizer\AwesomeCache\CacheData;

abstract class OrganizerObject {

	protected $bundle = null;
	protected $includes = array();
	protected $output = null;
	protected $version = null;
	

	public function __construct($bundle, array $includes, $version)
	{
		$this->bundle = $bundle;
		$this->includes = $includes;
		$this->version = $version;

		$objectType = $this->getType();
		$this->config = OZR::getConfig($objectType);
	}

	protected function getType () {
		$objectType = strtolower(basename(get_called_class()));
		$objectType = explode('\\', $objectType);
		$objectType = array_pop($objectType);
		return $objectType;
	}

	/**
	 * include a new file 
	 */
	public function add($item) {

		if(is_array($item))
		{
			$this->includes = array_merge($this->includes, $item);
		}
		else if(is_string($item) and !in_array($item, $this->includes))
		{
			$this->includes[] = $item;
		}
		return $this;
	}

	/**
	 * include a file before all other includes 
	 */
	public function addBefore($item) {
		if(is_array($item)) 
		{
			$this->includes = array_merge($item, $this->includes);
		}else if(is_string($item) and !in_array($item, $this->includes)) {
			array_unshift($this->includes, $item);
		}

		return $this;
	}

	/**
	 * Add code directly 
	 */
	public function addCode($string) {
		if(!is_string($string)) {
			throw new OrganizerException("Invalid code");
		}

		$code = (object) array(
			'type' 		=> 'embeded',
			'code'		=> $string
			);
		$this->includes[] = $code;
	}

	protected function signature() {
		return OZR::getConfig('signature')
		? (
			'/** '."\n".
			' * '.$this->bundle.' v'.$this->version.' | '.gmdate("M d Y H:i:s")." UTC\n".
			' * Organized by Organizer'."\n".
			' * https://github.com/kabir-baidhya/organizer'."\n".
			' */'."\n"

		) : null;
	}

	public function merge()
	{
		$merged = '';
		foreach($this->includes as $singleItem)
		{
			if(is_string($singleItem)) {

				$path = $this->config['basePath'].$singleItem.$this->extension;

				if(file_exists($path))
				{
					$code = file_get_contents($path);
				} else throw new OrganizerException("{$path} not found");
				
			} else if(is_object($singleItem) and @$singleItem->type == 'embeded') {
				$code = $singleItem->code;
			} else {
				throw new OrganizerException("Invalid Javacode");
			}

			$merged .= "\n".$code;
		}

		$this->output = $merged;
		return $this;
	}

	public function build()
	{

		$this->merge();

		$uniqueString = $this->uniqueBundleString();

		$item = new CacheData($uniqueString);
		$cacheEnabled = $this->config['cache'];

		if( !$cacheEnabled or !$item->isCachedAndUsable() )
		{
			$content = $this->config['minify'] ? $this->outputMinified() : $this->output();
			$item->putInCache($content);
		}

		$serverUrl = OZR::getConfig('serverUrl');
		$parameter = $this->config['parameter'];
		

		$url = $serverUrl.'?'.http_build_query(array(
			$parameter 		=>  $uniqueString,
			'ver'			=> $this->version
			));

		return $url;
	}

	protected function uniqueBundleString() {
		return base64_encode($this->getType().'-'.$this->bundle);
	}

	protected function preEmbedContent() {

		$cacheEnabled = $this->config['cache'];
		
		$uniqueString = $this->uniqueBundleString();

		$data = new CacheData($uniqueString);

		if($cacheEnabled and $data->isCachedAndUsable()) {
			$content = $data->cachedData();

		} else {
			
			$this->build();
			$content = $this->config['minify'] ? $this->outputMinified() : $this->output();
		}

		return $content;
	}

	abstract function includeHere();
	abstract function embedHere();
	abstract function output();
	abstract function outputMinified();
	
}